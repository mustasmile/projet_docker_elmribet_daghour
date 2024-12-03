CREATE TABLE workflow_decisions (
    id SERIAL PRIMARY KEY,
    decision_name VARCHAR(100) NOT NULL,
    description TEXT,
    decision_type VARCHAR(50),
    creator VARCHAR(100),
    status_creator VARCHAR(20),
    verifier VARCHAR(100),
    status_verifier VARCHAR(20),
    approver VARCHAR(100),
    status_approver VARCHAR(20)
);

CREATE TABLE responsibility_matrix (
    id SERIAL PRIMARY KEY,
    decision_type VARCHAR(255) NOT NULL,
    verifiers VARCHAR(255)[] NOT NULL,
    creators VARCHAR(255)[] NOT NULL,
    approvers VARCHAR(255)[] NOT NULL
);

INSERT INTO workflow_decisions (decision_name, description, decision_type, creator, status_creator, verifier, status_verifier, approver, status_approver)
VALUES
('Fortify the Village Walls', 'Reinforce the outer walls to defend against rival clans.', 'Defense', 'Chief Alric', 'Approved', 'Captain Elric', 'Pending', 'Elder Mara', 'Not Approved'),
('Summon Allied Clans', 'Call upon the allied clans for support in the upcoming war.', 'Diplomacy', 'Scribe Lina', 'Approved', 'Envoy Garrick', 'Approved', 'Chief Alric', 'Approved'),
('Expand the Grain Silos', 'Construct larger silos to store food for the winter.', 'Logistics', 'Chief Alric', 'Approved', 'Overseer Grelda', 'Approved', 'Elder Mara', 'Approved'),
('Train the Archers', 'Organize intensive training sessions for the clans archers.', 'Training', 'Captain Elric', 'Approved', 'Trainer Vyrin', 'Approved', 'Chief Alric', 'Pending');



INSERT INTO responsibility_matrix (decision_type, verifiers, creators, approvers) VALUES
('Political', ARRAY['Lady Elspeth', 'Sir Geoffrey'], ARRAY['Lord Alric', 'Lady Margaret'], ARRAY['High Chancellor', 'King Roland']),
('Infrastructure', ARRAY['Sir Cedric', 'Engineer Thomas'], ARRAY['Lord Duncan'], ARRAY['High Chancellor']),
('Military', ARRAY['General Marcus', 'Commander Leon'], ARRAY['Lord Alric'], ARRAY['King Roland']),
('Economic', ARRAY['Lady Margaret', 'Advisor Philip'], ARRAY['Lord Duncan', 'Lady Elspeth'], ARRAY['Queen Isabella']);
INSERT INTO responsibility_matrix (decision_type, verifiers, creators, approvers) VALUES
('Military Strategy', ARRAY['Sir Lancelot', 'Sir Gawain'], ARRAY['Sir Bedivere', 'Sir Kay'], ARRAY['King Arthur']),
('Economic Policy', ARRAY['Sir Percival', 'Sir Gareth'], ARRAY['Sir Tristan', 'Sir Bors'], ARRAY['King Arthur']),
('Diplomatic Mission', ARRAY['Sir Galahad', 'Sir Geraint'], ARRAY['Sir Agravain', 'Sir Lamorak'], ARRAY['King Arthur']),
('Infrastructure', ARRAY['Sir Palamedes', 'Sir Safir'], ARRAY['Sir Ector', 'Sir Dagonet'], ARRAY['King Arthur']),
('Cultural Event', ARRAY['Sir Griflet', 'Sir Brunor'], ARRAY['Sir Pelleas', 'Sir Elyan'], ARRAY['King Arthur']);

INSERT INTO workflow_decisions (decision_name, description, decision_type, creator, status_creator, verifier, status_verifier, approver, status_approver) VALUES
('Fortify Northern Border', 'Strengthen defenses against northern invaders.', 'Military Strategy', 'Sir Bedivere', 'Approved', 'Sir Lancelot', 'Approved', 'King Arthur', 'Approved'),
('Bridge over River Avon', 'Construct a bridge to improve trade routes.', 'Infrastructure', 'Sir Ector', 'Draft', 'Sir Palamedes', 'Pending', 'King Arthur', 'Pending'),
('Deploy Eastern Scouts', 'Gather intelligence on eastern movements.', 'Military Strategy', 'Sir Kay', 'Draft', 'Sir Gawain', 'Pending', 'King Arthur', 'Pending'),
('Upgrade Castle Defenses', 'Modernize the castle fortifications.', 'Military Strategy', 'Sir Bedivere', 'Draft', 'Sir Lancelot', 'Pending', 'King Arthur', 'Pending'),
('Supply Chain Improvement', 'Enhance supply routes for better resource distribution.', 'Economic Policy', 'Sir Tristan', 'Approved', 'Sir Percival', 'Approved', 'Queen Guinevere', 'Approved'),
('Annual Harvest Festival', 'Organize a festival to celebrate the harvest.', 'Cultural Event', 'Sir Pelleas', 'Draft', 'Sir Griflet', 'Pending', 'Queen Guinevere', 'Pending'),
('Tax Reform Proposal', 'Revise tax laws to boost the economy.', 'Economic Policy', 'Sir Bors', 'Draft', 'Sir Gareth', 'Pending', 'Queen Guinevere', 'Pending'),
('Alliance with Orkney', 'Form an alliance with the Kingdom of Orkney.', 'Diplomatic Mission', 'Sir Agravain', 'Draft', 'Sir Galahad', 'Pending', 'Lord Merlin', 'Pending'),
('Trade with Gaul', 'Establish trade routes with Gaul.', 'Diplomatic Mission', 'Sir Lamorak', 'Draft', 'Sir Geraint', 'Pending', 'Lord Merlin', 'Pending'),
('Aqueduct Expansion', 'Extend aqueducts to reach remote villages.', 'Infrastructure', 'Sir Dagonet', 'Draft', 'Sir Safir', 'Pending', 'King Arthur', 'Pending'),
('Knighting Ceremony', 'Host a ceremony to knight new warriors.', 'Cultural Event', 'Sir Elyan', 'Draft', 'Sir Brunor', 'Pending', 'Queen Guinevere', 'Pending'),
('Market Regulation', 'Implement new regulations for fair trade.', 'Economic Policy', 'Sir Tristan', 'Draft', 'Sir Percival', 'Pending', 'Queen Guinevere', 'Pending');

