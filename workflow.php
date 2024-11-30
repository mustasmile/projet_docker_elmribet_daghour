<?php
$host = 'postgres';
$dbname = 'feudal_clan';
$user = 'postgres';
$password = '968527';

$isAdmin = true;

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Connection to database failed: " . pg_last_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_decision') {
    $decision_name = pg_escape_string($_POST['decision_name']);
    $description = pg_escape_string($_POST['description']);
    $decision_type = pg_escape_string($_POST['decision_type']);
    $creator = pg_escape_string($_POST['creator']);
    $verifier = pg_escape_string($_POST['verifier']);
    $approver = pg_escape_string($_POST['approver']);

    $status_creator = 'Draft';
    $status_verifier = 'Pending';
    $status_approver = 'Pending';

    $insert_query = "INSERT INTO workflow_decisions (decision_name, description, decision_type, creator, status_creator, verifier, status_verifier, approver, status_approver) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";
    $result = pg_query_params($conn, $insert_query, array($decision_name, $description, $decision_type, $creator, $status_creator, $verifier, $status_verifier, $approver, $status_approver));

    if (!$result) {
        $error_message = "Error inserting data: " . pg_last_error($conn);
    } else {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$isAdmin) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    if ($_POST['action'] === 'update_status') {
        $id = intval($_POST['id']);
        $field = pg_escape_string($_POST['field']);
        $new_status = pg_escape_string($_POST['new_status']);

        if (in_array($field, ['status_creator', 'status_verifier', 'status_approver'])) {
            $update_query = "UPDATE workflow_decisions SET $field = $1 WHERE id = $2";
            $result = pg_query_params($conn, $update_query, array($new_status, $id));

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database update failed']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid field']);
        }
        exit();
    } elseif ($_POST['action'] === 'delete_decision') {
        $id = intval($_POST['id']);

        $delete_query = "DELETE FROM workflow_decisions WHERE id = $1";
        $result = pg_query_params($conn, $delete_query, array($id));

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete decision']);
        }
        exit();
    }
}

$query = "SELECT * FROM workflow_decisions";
$result = pg_query($conn, $query);
if (!$result) {
    die("Error fetching data: " . pg_last_error());
}

$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
}
pg_free_result($result);

$matrix_query = "SELECT decision_type, array_to_json(verifiers) as verifiers, array_to_json(creators) as creators, array_to_json(approvers) as approvers FROM responsibility_matrix";
$matrix_result = pg_query($conn, $matrix_query);
if (!$matrix_result) {
    die("Error fetching responsibility matrix: " . pg_last_error());
}

$responsibility_matrix = [];
$decision_types = [];
while ($row = pg_fetch_assoc($matrix_result)) {
    $decision_types[] = $row['decision_type'];

    $row['verifiers'] = json_decode($row['verifiers']);
    $row['creators'] = json_decode($row['creators']);
    $row['approvers'] = json_decode($row['approvers']);

    $responsibility_matrix[] = $row;
}
pg_free_result($matrix_result);
pg_close($conn);

$decisions_json = json_encode($data);
$responsibility_matrix_json = json_encode($responsibility_matrix);
$isAdmin_js = json_encode($isAdmin);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Workflow of Victory </title>
    <!-- AG Grid CSS -->
    <link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-grid.css">
    <link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-theme-quartz.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=Dancing+Script&family=Merriweather&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-image: url('photo3.png');
            background-size: cover;
            background-attachment: fixed;
            color: #f8f9fa;
            font-family: 'Merriweather', serif;
        }
        .overlay {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 50px;
            border-radius: 10px;
            margin-top: 50px;
        }
        h1 {
            font-family: 'Cinzel', serif;
            font-size: 3.5em;
            text-align: center;
            color: #ffd700;
            text-shadow: 2px 2px 5px #000;
        }
        p, li {
            font-size: 1.2em;
            color: #f8f9fa;
        }
        .signature {
            font-family: 'Dancing Script', cursive;
            font-size: 2em;
            text-align: right;
            margin-top: 50px;
            color: #ffc107;
        }
        .btn-custom {
            background-color: #d4af37;
            border: none;
            font-size: 1.2em;
            padding: 10px 30px;
            border-radius: 5px;
            color: #000;
            font-weight: bold;
            transition: background-color 0.3s ease;
            font-family: 'Cinzel', serif;
        }
        .btn-custom:hover {
            background-color: #c09330;
            color: #000;
        }
        .content-wrapper {
            min-height: 100vh;
        }
        .section-divider {
            border: 2px solid #ffc107;
            width: 50px;
            margin: 30px auto;
        }
        .ag-theme-quartz {
            height: 600px;
            width: 150%;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            margin-top: 30px;
        }
        #deliverablesGrid {
            width: 100%;
        }
        .ag-theme-quartz .ag-header {
            background-color: rgba(0, 0, 0, 0.8);
            color: #ffd700;
            font-family: 'Cinzel', serif;
            font-size: 1em;
        }
        .ag-theme-quartz .ag-header-cell-label {
            color: #ffd700;
        }
        .ag-theme-quartz .ag-cell {
            background-color: rgba(255, 255, 255, 0.8);
            color: #000;
            font-family: 'Merriweather', serif;
            font-size: 0.9em;
            white-space: normal;
        }
        .status-draft {
            color: #000;
            background: #f0e68c;
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status-pending {
            color: #fff;
            background: #ff8c00;
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status-approved {
            color: #fff;
            background: #28a745;
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status-not-approved {
            color: #fff;
            background: #dc3545;
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-delete {
            background-color: transparent;
            border: none;
            color: #dc3545;
            font-size: 1.2em;
            cursor: pointer;
        }
        .btn-delete:hover {
            color: #a71d2a;
        }
        .modal-header, .modal-footer {
            background-color: #343a40;
            color: #fff;
        }
        .form-control {
            font-family: 'Merriweather', serif;
        }
        label {
            font-weight: bold;
            color: #fff;
        }
        .btn-submit {
            background-color: #d4af37;
            color: #000;
            border: none;
            font-weight: bold;
            padding: 10px 20px;
            font-size: 1.1em;
        }
        .btn-submit:hover {
            background-color: #c09330;
            color: #000;
        }
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }
        .modal-content {
            background-color: rgba(0, 0, 0, 0.8);
            border: none;
        }
        .modal-content .form-control {
            background-color: rgba(255, 255, 255, 0.9);
            color: #000;
            border-radius: 5px;
        }
        .medieval-character {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .medieval-text-container {
            display: flex;
            align-items: flex-start;
        }
        .medieval-text-container img {
            margin-right: 20px;
        }
        .form-group label {
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        .modal-body {
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="container-fluid content-wrapper d-flex align-items-center">
        <div class="overlay w-100">
            <h1><i class="fas fa-crown"></i> Clan decisions Workflow</h1>
            <hr class="section-divider">
            <div class="medieval-text-container">
                <img src="medieval_character.png" alt="Alric the Wise" class="medieval-character">
                <div>
                    <p><strong>[Alric the Wise Speaks]</strong></p>
                    <p>
                        Greetings, my liege. Behold, before you, a marvel crafted by the mysterious traveler from distant lands—a tool unlike any other. They call it a "button," much like the royal seal upon a scroll; when pressed, it sets forth commands across the realm. By engaging it, you shall weave new decisions into the tapestry of our kingdom.
                    </p>
                    <p>
                        Each decree crafted here must bear the mark of a chosen creator, a vigilant verifier, and a noble approver. These roles are akin to our scribes who pen the orders, the knights who ensure their validity, and the councilors who grant their blessing. Upon your selection, they shall receive word through this enchanted device, swifter than any courier on horseback.
                    </p>
                    <p>
                        As the sovereign ruler, you possess the power to alter the course of these decisions at will. While others are bound by their roles, your wisdom grants you the ability to change the very fabric of our plans directly within this magical ledger. Simply by selecting a status, you can decree a new path forward, and it shall be so.
                    </p>
                    <p>
                        Moreover, should a decision no longer serve our cause, you hold the authority to remove it entirely, just as a king may strike a decree from the annals. Use this power wisely, for with great authority comes great responsibility.
                    </p>
                    <p>
                        Through this mystical workflow, we can coordinate our efforts, manage our resources, and unite our people under your wise leadership. It is the compass by which we navigate the seas of governance, ensuring every endeavor sails smoothly towards prosperity.
                    </p>
                    <p>
                        Remember, every monumental achievement begins with a single command. Let us embrace this tool, for it shall be the quill that pens our legacy in the annals of history.
                    </p>
                </div>
            </div>
            <div class="text-center mb-3">
                <button class="btn btn-custom" data-toggle="modal" data-target="#createDecisionModal">Add New Decision</button>
            </div>
            <div id="deliverablesGrid" class="ag-theme-quartz"></div>
        </div>
    </div>

    <div class="modal fade" id="createDecisionModal" tabindex="-1" aria-labelledby="createDecisionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 style="font-family: 'Cinzel', serif;" class="modal-title" id="createDecisionModalLabel">Create New Decision</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="POST" action="">
              <div class="modal-body">
                  <input type="hidden" name="action" value="add_decision">
                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <label for="decision_name">Decision Name</label>
                          <input type="text" class="form-control" id="decision_name" name="decision_name" required>
                      </div>
                      <div class="form-group col-md-6">
                          <label for="decision_type">Decision Type</label>
                          <select class="form-control" id="decision_type" name="decision_type" required>
                              <option value="">Select Decision Type</option>
                              <?php
                              foreach ($decision_types as $type) {
                                  echo '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars($type) . '</option>';
                              }
                              ?>
                          </select>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="description">Description</label>
                      <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                  </div>
                  <div class="form-row">
                      <div class="form-group col-md-4">
                          <label for="creator">Creator</label>
                          <select class="form-control" id="creator" name="creator" required>
                              <option value="">Select Creator</option>
                          </select>
                      </div>
                      <div class="form-group col-md-4">
                          <label for="verifier">Verifier</label>
                          <select class="form-control" id="verifier" name="verifier" required>
                              <option value="">Select Verifier</option>
                          </select>
                      </div>
                      <div class="form-group col-md-4">
                          <label for="approver">Approver</label>
                          <select class="form-control" id="approver" name="approver" required>
                              <option value="">Select Approver</option>
                          </select>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-submit">Save Decision</button>
              </div>
          </form>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.noStyle.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        const responsibilityMatrix = <?= $responsibility_matrix_json ?>;
        const isAdmin = <?= $isAdmin_js ?>;

        const statusOptions = ['Draft', 'Pending', 'Approved', 'Not Approved'];

        const columnDefs = [
            { headerName: "Decision Name", field: "decision_name", sortable: true, filter: true },
            {
                headerName: "Description",
                field: "description",
                sortable: true,
                filter: true,
                width: 400
            },
            { headerName: "Type", field: "decision_type", sortable: true, filter: true },
            { headerName: "Creator", field: "creator", sortable: true, filter: true },
            {
                headerName: "Creator Status",
                field: "status_creator",
                cellRenderer: statusRenderer,
                width: 150,
                sortable: true,
                editable: isAdmin,
                cellEditor: 'agSelectCellEditor',
                cellEditorParams: {
                    values: statusOptions
                }
            },
            { headerName: "Verifier", field: "verifier", sortable: true, filter: true },
            {
                headerName: "Verifier Status",
                field: "status_verifier",
                cellRenderer: statusRenderer,
                sortable: true,
                width: 150,
                editable: isAdmin,
                cellEditor: 'agSelectCellEditor',
                cellEditorParams: {
                    values: statusOptions
                }
            },
            { headerName: "Approver", field: "approver", sortable: true, filter: true },
            {
                headerName: "Approver Status",
                field: "status_approver",
                cellRenderer: statusRenderer,
                sortable: true,
                editable: isAdmin,
                width: 150,
                cellEditor: 'agSelectCellEditor',
                cellEditorParams: {
                    values: statusOptions
                }
            },
            {
                headerName: "",
                field: "delete",
                width: 40,
                cellRenderer: function(params) {
                    if (isAdmin) {
                        return '<button class="btn-delete" onclick="deleteDecision(' + params.data.id + ')"><i class="fas fa-trash-alt"></i></button>';
                    } else {
                        return '';
                    }
                }
            }
        ];

        function statusRenderer(params) {
            const statusMap = {
                'Draft': "status-draft",
                'Pending': "status-pending",
                'Approved': "status-approved",
                'Not Approved': "status-not-approved"
            };
            const cssClass = statusMap[params.value];
            if (cssClass) {
                return `<span class="${cssClass}">${params.value}</span>`;
            } else {
                return params.value || '';
            }
        }

        const rowData = <?= $decisions_json ?>;

        const gridOptions = {
            columnDefs: columnDefs,
            rowData: rowData,
            defaultColDef: {
                resizable: true,
                sortable: true,
                filter: true,
            },
            onGridReady: function(params) {
                params.api.sizeColumnsToFit();
                autoSizeAllColumns(params.columnApi);
            },
            stopEditingWhenGridLosesFocus: true,
            onCellValueChanged: onCellValueChanged,
        };
        function autoSizeAllColumns(columnApi) {
        const allColumnIds = [];
        columnApi.getAllColumns().forEach((column) => {
            allColumnIds.push(column.getId());
        });
        columnApi.autoSizeColumns(allColumnIds);
    }
        const gridDiv = document.querySelector('#deliverablesGrid');
        new agGrid.Grid(gridDiv, gridOptions);

        window.addEventListener('resize', function() {
            gridOptions.api.sizeColumnsToFit();
        });

        document.getElementById('decision_type').addEventListener('change', function() {
            const selectedType = this.value;

            const entry = responsibilityMatrix.find(item => item.decision_type === selectedType);

            if (entry) {
                populateSelect('creator', entry.creators);
                populateSelect('verifier', entry.verifiers);
                populateSelect('approver', entry.approvers);
            } else {
                clearSelect('creator');
                clearSelect('verifier');
                clearSelect('approver');
            }
        });

        function populateSelect(elementId, optionsArray) {
            const selectElement = document.getElementById(elementId);
            selectElement.innerHTML = '<option value="">Select ' + capitalizeFirstLetter(elementId) + '</option>';
            optionsArray.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option;
                opt.textContent = option;
                selectElement.appendChild(opt);
            });
        }

        function clearSelect(elementId) {
            const selectElement = document.getElementById(elementId);
            selectElement.innerHTML = '<option value="">Select ' + capitalizeFirstLetter(elementId) + '</option>';
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function onCellValueChanged(params) {
            if (!isAdmin) return;

            const data = params.data;
            const field = params.colDef.field;
            const newValue = params.newValue;

            if (['status_creator', 'status_verifier', 'status_approver'].includes(field)) {
                $.ajax({
                    url: '',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'update_status',
                        id: data.id,
                        field: field,
                        new_status: newValue
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Status updated successfully');
                        } else {
                            params.node.setDataValue(field, params.oldValue);
                            alert('Failed to update status: ' + response.message);
                        }
                    },
                    error: function() {
                        params.node.setDataValue(field, params.oldValue);
                        alert('An error occurred while updating the status.');
                    }
                });
            }
        }

        function deleteDecision(id) {
            if (confirm('Are you sure you want to delete this decision?')) {
                $.ajax({
                    url: '',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'delete_decision',
                        id: id
                    },
                    success: function(response) {
                        if (response.success) {
                            const rowNode = gridOptions.api.getRowNode(id.toString());
                            gridOptions.api.applyTransaction({ remove: [rowNode.data] });
                            console.log('Decision deleted successfully');
                        } else {
                            alert('Failed to delete decision: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while deleting the decision.');
                    }
                });
            }
        }
    </script>
</body>
</html>
