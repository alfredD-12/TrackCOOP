<?php
session_start();
include('../auth/db_connect.php');

// Security check: Admin and Bookkeeper only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

/** ── MEMBER RISK DASHBOARD ENGINE ── **/

// Static member risk intelligence data
$static_members_intel = [
    ['id' => 1, 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'sector' => 'Rice', 'days_since' => 5, 'contribution_count' => 3, 'risk_level' => 'Low', 'predicted_status' => 'Likely Active', 'interpretation' => 'Stable participation trend.', 'intervention' => false],
    ['id' => 2, 'first_name' => 'Maria', 'last_name' => 'Santos', 'sector' => 'Corn', 'days_since' => 15, 'contribution_count' => 8, 'risk_level' => 'Low', 'predicted_status' => 'Likely Active', 'interpretation' => 'Stable participation trend.', 'intervention' => false],
    ['id' => 3, 'first_name' => 'Pedro', 'last_name' => 'Garcia', 'sector' => 'Fishery', 'days_since' => 45, 'contribution_count' => 2, 'risk_level' => 'Medium', 'predicted_status' => 'Retention Risk', 'interpretation' => 'Developing delay pattern (>45 days).', 'intervention' => true],
    ['id' => 4, 'first_name' => 'Rosa', 'last_name' => 'Lopez', 'sector' => 'Livestock', 'days_since' => 60, 'contribution_count' => 1, 'risk_level' => 'High', 'predicted_status' => 'Dormancy Forecast', 'interpretation' => 'Critical gap in activity (>60 days).', 'intervention' => true],
];

$members_intel = $static_members_intel;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Risk Dashboard | TrackCOOP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --track-green: #206970;
            --track-dark: #1e293b;
            --track-bg: #f8fafc;
            --white: #ffffff;
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        body {
            background-color: var(--track-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--track-dark);
            padding: 30px;
            overflow-x: hidden;
        }

        /* Jakob's 10: Aesthetic and minimalist design */
        h2.page-title { font-weight: 900; letter-spacing: -1px; margin-bottom: 30px; color: #20a060; }

        /* Feature Cards Grid */
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 40px; }
        
        .risk-card {
            background: var(--white);
            border-radius: 24px;
            padding: 30px;
            border: 2px solid #20a060;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            z-index: 1;
        }
        .risk-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 40px rgba(32, 160, 96, 0.15); 
            background: #fafff9;
        }
        .risk-card.active { border: 2.5px solid #20a060; background: #fafff9; }

        .card-icon { 
            width: 60px; height: 60px; 
            background: rgba(32, 160, 96, 0.12); color: #20a060;
            border: 1.5px solid rgba(32, 160, 96, 0.2);
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; margin-bottom: 20px;
        }
        
        .card-label { font-weight: 800; font-size: 1.1rem; margin-bottom: 10px; line-height: 1.2; color: #20a060; }
        .card-desc { font-size: 0.85rem; color: #000; font-weight: 500; margin-bottom: 20px; }

        .btn-view {
            background: #20a060; color: #fff; border: 2px solid #20a060;
            padding: 8px 20px; border-radius: 50px; font-weight: 800; font-size: 0.75rem;
            text-transform: uppercase; transition: var(--transition);
            box-shadow: 0 5px 15px rgba(32, 160, 96, 0.2);
        }
        .btn-view:hover {
            background: #1b8a53; color: #fff; border-color: #1b8a53; transform: translateY(-2px);
        }
        .risk-card:hover .btn-view, .risk-card.active .btn-view { background: #20a060; color: #fff; border-color: #20a060; }

        /* Workspace Pane Replacement - Dynamic View Design */
        .workspace-header { border-left: none; padding-left: 0; margin-bottom: 25px; }
        .workspace-header h4 { font-weight: 900; margin-bottom: 6px; letter-spacing: -1.5px; font-size: 2.2rem; color: #20a060; }
        #workDesc { color: #000 !important; opacity: 1; }

        .intel-table { width: 100%; border-collapse: separate; border-spacing: 0 15px; }
        .intel-table th { background: transparent; padding: 10px 25px; font-weight: 900; font-size: 0.8rem; color: #000; text-transform: uppercase; letter-spacing: 2px; }
        .intel-table td { 
            background: #fff; 
            padding: 25px; 
            border-top: 1px solid #f1f5f9; 
            border-bottom: 1px solid #f1f5f9; 
            vertical-align: middle;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            transition: var(--transition);
        }
        .intel-table tr:hover td { transform: scale(1.015); box-shadow: 0 10px 25px rgba(0,0,0,0.05); z-index: 2; border-color: rgba(32, 160, 96, 0.2); }
        .intel-table tr td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 20px; border-bottom-left-radius: 20px; }
        .intel-table tr td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 20px; border-bottom-right-radius: 20px; }
        
        .badge-pill { padding: 10px 18px; border-radius: 50px; font-weight: 900; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }
        .badge-High { background: #fee2e2; color: #b91c1c; }
        .badge-Medium { background: #ffedd5; color: #9a3412; }
        .badge-Low { background: #dcfce7; color: #166534; }

        /* Elite Back Button */
        .btn-back {
            background: white;
            border: 2px solid #20a060;
            color: #20a060;
            width: 50px; height: 50px;
            border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            transition: var(--transition);
            box-shadow: 0 5px 15px rgba(32, 160, 96, 0.1);
        }
        .btn-back:hover {
            background: #20a060;
            color: white;
            transform: translateX(-5px) scale(1.1);
            box-shadow: 0 10px 25px rgba(32, 160, 96, 0.2);
        }

        /* Entrance Animations */
        @keyframes slideInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        .stagger-1 { animation: slideInUp 0.6s ease-out forwards; animation-delay: 0.1s; opacity: 0; }
        .stagger-2 { animation: slideInUp 0.6s ease-out forwards; animation-delay: 0.2s; opacity: 0; }
        .stagger-3 { animation: slideInUp 0.6s ease-out forwards; animation-delay: 0.3s; opacity: 0; }
        .stagger-4 { animation: slideInUp 0.6s ease-out forwards; animation-delay: 0.4s; opacity: 0; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="feature-grid">
        <!-- Feature 1: Status Prediction -->
        <div class="risk-card stagger-1" onclick="loadFeature('status', this)">
            <div class="card-icon"><i class="bi bi-person-badge"></i></div>
            <div class="card-label">View Predicted Member Status</div>
            <div class="card-desc">Forecasts member activity status based on real-time contribution patterns.</div>
            <button class="btn-view">Access Predictions</button>
        </div>

        <!-- Feature 2: Risk Classification -->
        <div class="risk-card stagger-2" onclick="loadFeature('risk', this)">
            <div class="card-icon"><i class="bi bi-shield-check"></i></div>
            <div class="card-label">Understand Risk Classification</div>
            <div class="card-desc">Categorizes membership into risk segments for structured auditing.</div>
            <button class="btn-view">Analyze Risks</button>
        </div>

        <!-- Feature 3: Result Interpretation -->
        <div class="risk-card stagger-3" onclick="loadFeature('interpret', this)">
            <div class="card-icon"><i class="bi bi-journal-check"></i></div>
            <div class="card-label">Interpret Prediction Results</div>
            <div class="card-desc">AI-generated interpretation of data-driven behavior trends.</div>
            <button class="btn-view">Explain Metrics</button>
        </div>

        <!-- Feature 4: Intervention Manager -->
        <div class="risk-card stagger-4" onclick="loadFeature('intervene', this)">
            <div class="card-icon"><i class="bi bi-exclamation-octagon"></i></div>
            <div class="card-label">Identify Members Requiring Intervention</div>
            <div class="card-desc">Priority targets for outreach to prevent long-term dormancy.</div>
            <button class="btn-view">Focus Action</button>
        </div>
    </div>

    <!-- Dynamic Detail View (In-place) -->
    <div id="dynamicView" style="display: none;" class="stagger-1 mt-4">
        <div class="d-flex align-items-center mb-5">
            <button onclick="backToGrid()" class="btn-back me-4">
                <i class="bi bi-arrow-left"></i>
            </button>
            <div class="workspace-header m-0">
                <h4 id="workTitle" class="mb-0">Diagnostic Detail Area</h4>
                <p class="text-muted small fw-600 m-0" id="workDesc">Select a feature card above to view real-time prescriptive analytics.</p>
            </div>
        </div>
        
        <div id="workspaceContent" class="table-responsive">
            <!-- Dynamic Data Tables Load Here -->
        </div>


    </div>

    <script>
    const members = <?php echo json_encode($members_intel); ?>;

    function loadFeature(type, card) {
        const grid = document.querySelector('.feature-grid');
        const view = document.getElementById('dynamicView');
        const title = document.getElementById('workTitle');
        const desc = document.getElementById('workDesc');
        const space = document.getElementById('workspaceContent');

        // Dynamic Switching Logic
        grid.style.display = 'none';
        view.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });

        space.innerHTML = '';

        let tbl = `<table class="intel-table"><thead><tr><th style="width:30%">Intelligence Subject</th>`;

        if (type === 'status') {
            title.innerText = "Member Status Predictions";
            desc.innerText = "Live AI forecasting of member engagement for the next quarter.";
            tbl += `<th>Predicted Activity</th><th>Last Action</th></tr></thead><tbody>`;
            members.forEach(m => {
                tbl += `<tr>
                    <td><strong>${m.first_name} ${m.last_name}</strong><br><small class="text-success fw-bold">${m.sector || 'General'}</small></td>
                    <td><span class="fw-900 text-dark">${m.predicted_status}</span></td>
                    <td>${m.last_contribution || 'NO RECORD'}</td>
                </tr>`;
            });
        } 
        else if (type === 'risk') {
            title.innerText = "Risk Classification Matrix";
            desc.innerText = "Segmentation of members based on contribution stability metrics.";
            tbl += `<th>Classification</th><th>Segment Audit</th></tr></thead><tbody>`;
            members.forEach(m => {
                tbl += `<tr>
                    <td><strong>${m.first_name} ${m.last_name}</strong></td>
                    <td><span class="badge-pill badge-${m.risk_level}">${m.risk_level} RISK</span></td>
                    <td><span class="fw-700 ${m.risk_level === 'High' ? 'text-danger' : 'text-muted'}">${m.risk_level === 'High' ? 'Critical Priority' : (m.risk_level === 'Medium' ? 'Monitor Gaps' : 'Maintaining Base')}</span></td>
                </tr>`;
            });
        }
        else if (type === 'interpret') {
            title.innerText = "Prediction Results Interpretation";
            desc.innerText = "Prescriptive logic explaining the reasoning behind each member forecast.";
            tbl += `<th>Automated Analysis</th></tr></thead><tbody>`;
            members.forEach(m => {
                tbl += `<tr>
                    <td><strong>${m.first_name} ${m.last_name}</strong></td>
                    <td class="fw-600 text-muted">${m.interpretation}</td>
                </tr>`;
            });
        }
        else if (type === 'intervene') {
            title.innerText = "Intervention Target List";
            desc.innerText = "Automated identification of members with high-priority outreach needs.";
            tbl += `<th>Action Status</th></tr></thead><tbody>`;
            const targets = members.filter(m => m.intervention);
            if(targets.length === 0) {
                tbl += `<tr><td colspan="2" class="text-center py-5 fw-bold text-muted">No members currently meet the threshold for intervention.</td></tr>`;
            } else {
                targets.forEach(m => {
                    tbl += `<tr>
                        <td><strong>${m.first_name} ${m.last_name}</strong></td>
                        <td class="text-danger fw-900"><i class="bi bi-lightning-fill"></i> URGENT ACTION REQUIRED</td>
                    </tr>`;
                });
            }
        }

        tbl += `</tbody></table>`;
        space.innerHTML = tbl;
    }

    function backToGrid() {
        const grid = document.querySelector('.feature-grid');
        const view = document.getElementById('dynamicView');
        
        // Return to Grid Logic
        view.style.display = 'none';
        grid.style.display = 'grid';
        
        // Remove active highlights
        document.querySelectorAll('.risk-card').forEach(c => c.classList.remove('active'));
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    </script>

</body>
</html>
