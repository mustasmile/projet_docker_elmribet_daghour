<?php
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

// Connecter à PostgreSQL
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Connection to database failed: " . pg_last_error());
}

$kingName = 'King Arthur';

$query = "SELECT COUNT(*) AS pending_approvals FROM workflow_decisions WHERE approver = $1 AND status_approver != 'Approved'";
$result = pg_query_params($conn, $query, array($kingName));
if (!$result) {
    die("Error fetching data: " . pg_last_error());
}
$row = pg_fetch_assoc($result);
$pendingApprovals = $row['pending_approvals'];
pg_free_result($result);
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Workflow of Victory - Welcome, <?php echo htmlspecialchars($kingName); ?>!</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=Dancing+Script&family=Merriweather&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
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
            background-color: #6c757d;
            border: none;
            font-size: 1.2em;
            padding: 10px 30px;
            border-radius: 5px;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #5a6268;
        }
        .navbar-custom {
            background-color: rgba(0, 0, 0, 0.9);
        }
        .navbar-custom .nav-link {
            color: #ffd700;
            font-size: 1.2em;
            text-shadow: 1px 1px 2px #000;
        }
        .navbar-custom .nav-link:hover {
            color: #fff;
        }
        .content-wrapper {
            min-height: 100vh;
        }
        .section-divider {
            border: 2px solid #ffc107;
            width: 50px;
            margin: 30px auto;
        }
        .btn-golden {
            background-color: #d4af37;
            color: #000;
            font-weight: bold;
            margin: 5px;
        }
        .btn-golden:hover {
            background-color: #c09330;
        }
        .image-wrapper img {
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
        }
        .medieval-text-container {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        .medieval-text-container img {
            max-width: 200px;
            margin-right: 20px;
            border-radius: 10px;
        }
        .pending-approvals {
            font-size: 2em;
            color: #ffd700;
            text-align: center;
            margin-top: 30px;
            text-shadow: 1px 1px 3px #000;
        }
        .pending-approvals a {
            color: #ffd700;
            text-decoration: underline;
        }
        .pending-approvals a:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand text-light" href="#">
                <i class="fas fa-dragon"></i> Workflow of Victory
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="workflow.php">Workflow of Decisions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="weapons_database.html">Database of Weapons</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container content-wrapper d-flex align-items-center">
        <div class="overlay w-100">
            <h1><i class="fas fa-crown"></i> Welcome, Noble <?php echo htmlspecialchars($kingName); ?>!</h1>
            <hr class="section-divider">
            <div class="medieval-text-container">
                <img src="medieval_character.png" alt="Alric the Wise">
                <div>
                    <p><strong>[Alric the Wise Speaks]</strong></p>
                    <p>
                        Greetings, mighty <?php echo htmlspecialchars($kingName); ?>. It is I, Alric the Wise, your loyal advisor and keeper of ancient secrets. Before you lies a tool like no other—an artifact not born of this age, but gifted by a traveler from a future realm. A realm where warriors battle not with blades alone, but with precision, order, and knowledge.
                    </p>
                    <p>
                        This tool, my lord, is the <strong>Workflow of Victory</strong>. It is no mere trinket; it is the key to your dominion. Within these enchanted pages, you shall find the means to guide your scribes, your warriors, and your emissaries with unmatched clarity. Here’s how it shall aid you:
                    </p>
                    <ol>
                        <li><strong>Ritual of Assignments:</strong> Ensure every task, whether small or mighty, is delivered to the right hands.</li>
                        <li><strong>Chain of Validation:</strong> Every decision and plan shall be reviewed, verified, and blessed by those you trust most.</li>
                        <li><strong>Tome of Completion:</strong> Witness the progress of your clan in real time, as tasks are completed and victories secured.</li>
                    </ol>
                    <p>
                        Furthermore, the <strong>Database of Weapons</strong> is at your disposal. This compendium holds knowledge of every blade, bow, and siege engine within our arsenal. Delve into its depths to make informed decisions about equipping your armies and strategizing for battles ahead.
                    </p>
                    <p class="pending-approvals">
                        As the sovereign ruler, you have <strong><?php echo $pendingApprovals; ?> decisions</strong> awaiting your esteemed approval. Please proceed to the <a href="workflow.php">Workflow of Decisions</a> to attend to these affairs.
                    </p>
                    <p>
                        Are you ready to claim your destiny, noble <?php echo htmlspecialchars($kingName); ?>? Then let us begin. Select your path, and the Workflow of Victory shall illuminate the way to greatness.
                    </p>
                    <p class="signature">
                        May the winds of fortune forever fill your sails,<br>
                        <strong>Alric the Wise</strong>
                    </p>
                </div>
            </div>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
