<?php
session_start();
include('../auth/db_connect.php');

// Security check: Admin and Bookkeeper only
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Bookkeeper'])) {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

/** ── MEMBER RISK DASHBOARD ENGINE ── **/

// Fetch live member intelligence data
$sql = "SELECT u.id, u.first_name, u.last_name, u.sector, 
               MAX(sc.created_at) as last_contribution,
               COUNT(sc.id) as contribution_count
        FROM users u
        LEFT JOIN share_capital sc ON u.id = sc.user_id
        WHERE u.role = 'Member' AND u.status = 'Approved'
        GROUP BY u.id";

$result = $conn->query($sql);
$members_intel = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $days_since = -1;
        if ($row['last_contribution']) {
            $last_date = new DateTime($row['last_contribution']);
            $now = new DateTime();
            $interval = $now->diff($last_date);
            $days_since = $interval->days;
        }

        // Behavior Logic (Prescriptive)
        $risk_level = "Low";
        $predicted_status = "Likely Active";
        $interpretation = "Stable participation trend.";
        $intervention = false;

        if ($days_since === -1) {
            $risk_level = "High"; $predicted_status = "Initial Dormancy"; 
            $interpretation = "No contributions since account opening."; $intervention = true;
        } elseif ($days_since > 90) {
            $risk_level = "High"; $predicted_status = "Dormancy Forecast"; 
            $interpretation = "Critical gap in activity (>90 days)."; $intervention = true;
        } elseif ($days_since > 45) {
            $risk_level = "Medium"; $predicted_status = "Retention Risk"; 
            $interpretation = "Developing delay pattern (>45 days)."; $intervention = true;
        }

        $row['risk_level'] = $risk_level;
        $row['predicted_status'] = $predicted_status;
        $row['interpretation'] = $interpretation;
        $row['intervention'] = $intervention;
        
        $members_intel[] = $row;
    }
}
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
            --track-green: #20a060;
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
        h2.page-title { font-weight: 800; letter-spacing: -1px; margin-bottom: 30px; }

        /* Feature Cards Grid */
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 40px; }
        
        .risk-card {
            background: var(--white);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            z-index: 1;
        }
        .risk-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(32, 160, 96, 0.08); border-color: var(--track-green); }
        .risk-card.active { border: 2.5px solid var(--track-green); background: #fafff9; }

        .card-icon { 
            width: 60px; height: 60px; 
            background: rgba(32, 160, 96, 0.1); color: var(--track-green);
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; margin-bottom: 20px;
        }
        
        .card-label { font-weight: 800; font-size: 1.1rem; margin-bottom: 10px; line-height: 1.2; }
        .card-desc { font-size: 0.85rem; color: #64748b; font-weight: 500; margin-bottom: 20px; }

        .btn-view {
            background: #fff; color: var(--track-green); border: 2px solid var(--track-green);
            padding: 8px 20px; border-radius: 50px; font-weight: 800; font-size: 0.75rem;
            text-transform: uppercase; transition: var(--transition);
        }
        .risk-card:hover .btn-view, .risk-card.active .btn-view { background: var(--track-green); color: #fff; }

        /* Workspace Pane */
        #workspacePane { opacity: 0; transform: translateY(20px); transition: var(--transition); }
        #workspacePane.visible { opacity: 1; transform: translateY(0); }

        .workspace-header { border-left: 6px solid var(--track-green); padding-left: 20px; margin-bottom: 30px; }
        .workspace-header h4 { font-weight: 800; margin-bottom: 4px; }

        .intel-table { width: 100%; surface: #fff; border-collapse: separate; border-spacing: 0 12px; }
        .intel-table th { background: transparent; padding: 10px 20px; font-weight: 800; font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .intel-table td { background: #fff; padding: 20px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .intel-table tr td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 16px; border-bottom-left-radius: 16px; }
        .intel-table tr td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 16px; border-bottom-right-radius: 16px; }
        
        .badge-pill { padding: 6px 14px; border-radius: 50px; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; }
        .badge-High { background: #fee2e2; color: #b91c1c; }
        .badge-Medium { background: #ffedd5; color: #9a3412; }
        .badge-Low { background: #dcfce7; color: #166534; }

        /* Entrance Animations */
        @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .stagger-1 { animation: slideIn 0.5s ease-out forwards; animation-delay: 0.1s; opacity: 0; }
        .stagger-2 { animation: slideIn 0.5s ease-out forwards; animation-delay: 0.2s; opacity: 0; }
        .stagger-3 { animation: slideIn 0.5s ease-out forwards; animation-delay: 0.3s; opacity: 0; }
        .stagger-4 { animation: slideIn 0.5s ease-out forwards; animation-delay: 0.4s; opacity: 0; }

        /* Secondary Module (Image Parity) */
        .system-audit { background: #fff; border-radius: 20px; padding: 25px; border: 2px solid #333; margin-top: 60px; overflow: hidden; }
        .audit-table { width: 100%; border-collapse: collapse; }
        .audit-table th, .audit-table td { border: 1.5px solid #333; padding: 15px; text-align: center; }
        .audit-table th { background: #fdfdfd; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; }
    </style>
</head>
<body>

<div class="container-fluid">
    <h2 class="page-title stagger-1">Member Risk Dashboard</h2>

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

    <!-- Workspace Detail Pane -->
    <div id="workspacePane">
        <div class="workspace-header">
            <h4 id="workTitle">Diagnostic Detail Area</h4>
            <p class="text-muted small fw-600 m-0" id="workDesc">Select a feature card above to view real-time prescriptive analytics.</p>
        </div>
        
        <div id="workspaceContent" class="table-responsive">
            <!-- Dynamic Data Tables Load Here -->
        </div>
    </div>

    <script>
    const members = <?php echo json_encode($members_intel); ?>;

    function loadFeature(type, card) {
        // Aesthetic: Highlight active card
        document.querySelectorAll('.risk-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');

        const pane = document.getElementById('workspacePane');
        const title = document.getElementById('workTitle');
        const desc = document.getElementById('workDesc');
        const space = document.getElementById('workspaceContent');

        pane.classList.add('visible');
        space.innerHTML = '';

        let tbl = `<table class="intel-table"><thead><tr><th style="width:30%">Intelligence Subject</th>`;

        if (type === 'status') {
            title.innerText = "Member Status Predictions";
            desc.innerText = "Live AI forecasting of member engagement for the next quarter.";
            tbl += `<th>Predicted Activity</th><th>Last Action</th></tr></thead><tbody>`;
            members.forEach(m => {
                tbl += `<tr>
                    <td><strong>${m.first_name} ${m.last_name}</strong><br><small class="text-success fw-bold">${m.sector || 'General'}</small></td>
                    <td><span class="fw-800 text-dark">${m.predicted_status}</span></td>
                    <td>${m.last_contribution || 'NO RECORD'}</td>
                </tr>`;
            });
        } 
        else if (type === 'risk') {
            title.innerText = "Risk Classification matrix";
            desc.innerText = "Segmentation of members based on contribution stability metrics.";
            tbl += `<th>Classification</th><th>Segment Audit</th></tr></thead><tbody>`;
            members.forEach(m => {
                tbl += `<tr>
                    <td><strong>${m.first_name} ${m.last_name}</strong></td>
                    <td><span class="badge-pill badge-${m.risk_level}">${m.risk_level} RISK</span></td>
                    <td>${m.risk_level === 'High' ? 'Critical Priority' : (m.risk_level === 'Medium' ? 'Monitor Gaps' : 'Maintaining Base')}</td>
                </tr>`;
            });
        }
        else if (type === 'interpret') {
            title.innerText = "Prediction Results interpretation";
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
                        <td class="text-danger fw-800"><i class="bi bi-lightning-fill"></i> URGENT ACTION REQUIRED</td>
                    </tr>`;
                });
            }
        }

        tbl += `</tbody></table>`;
        space.innerHTML = tbl;

        // User Control: Smooth scroll to workspace
        pane.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
</script>

</body>
</html>
