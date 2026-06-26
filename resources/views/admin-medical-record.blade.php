<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Record Viewer - Khayra Admin</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #1f2937;
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.10), transparent 30%),
                linear-gradient(180deg, #f6fbfa 0%, #eef7f5 100%);
        }

        .layout {
            min-height: 100vh;
            display: flex;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 32px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 22px;
        }

        .page-title {
            margin: 0;
            font-size: 38px;
            color: #0f766e;
            line-height: 1.1;
        }

        .page-subtitle {
            margin: 10px 0 0;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.7;
            max-width: 860px;
        }

        .back-link {
            display: inline-block;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 14px;
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
            font-weight: 700;
            white-space: nowrap;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.35fr .85fr;
            gap: 18px;
            margin-bottom: 22px;
        }

        .hero-main,
        .hero-side {
            border-radius: 28px;
            padding: 26px;
            box-shadow: 0 18px 42px rgba(15,118,110,.08);
        }

        .hero-main {
            background: linear-gradient(135deg, #0f766e 0%, #2f7f74 100%);
            color: white;
        }

        .hero-side {
            background: white;
            border: 1px solid #e5f1ee;
        }

        .hero-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            color: white;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .hero-title {
            margin: 0;
            font-size: 36px;
            line-height: 1.08;
            font-weight: 800;
        }

        .hero-text {
            margin: 14px 0 0;
            line-height: 1.8;
            font-size: 15px;
            color: rgba(255,255,255,.92);
            max-width: 760px;
        }

        .hero-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-tag {
            display: inline-block;
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
            color: white;
            font-size: 13px;
            font-weight: 700;
        }

        .side-title {
            margin: 0;
            font-size: 22px;
            color: #0f766e;
        }

        .side-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .mini-grid {
            display: grid;
            gap: 12px;
        }

        .mini-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .mini-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }

        .mini-value {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            line-height: 1.6;
            word-break: break-word;
        }

        .notice {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #cfe0ff;
            border-radius: 16px;
            padding: 14px 16px;
            margin-bottom: 22px;
            line-height: 1.7;
            font-size: 14px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 20px;
        }

        .section-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .section-title {
            margin: 0;
            font-size: 24px;
            color: #0f766e;
        }

        .section-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .profile-stack {
            display: grid;
            gap: 14px;
        }

        .profile-box,
        .data-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .profile-label,
        .data-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .profile-value,
        .data-value {
            font-size: 15px;
            color: #111827;
            line-height: 1.7;
            word-break: break-word;
        }

        .viewer-section {
            margin-bottom: 24px;
            padding-bottom: 22px;
            border-bottom: 1px solid #e8f1ef;
        }

        .viewer-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .viewer-title {
            margin: 0 0 8px;
            font-size: 20px;
            color: #0f766e;
            font-weight: 800;
        }

        .viewer-text {
            margin: 0 0 18px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .status-pill {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            text-transform: capitalize;
        }

        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #fef3c7; color: #92400e; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .empty-block {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 18px;
            color: #64748b;
            text-align: center;
            line-height: 1.7;
        }

        .repeat-wrap {
            display: grid;
            gap: 14px;
        }

        .repeat-item {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .repeat-head {
            font-size: 14px;
            font-weight: 800;
            color: #0f766e;
            margin-bottom: 12px;
        }

        .spacer {
            margin-top: 14px;
        }

        @media (max-width: 1180px) {
            .hero,
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .grid-2,
            .grid-3 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 1024px) {
            .layout {
                display: block;
            }

            .main {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 32px;
            }

            .hero-main,
            .hero-side,
            .section-card {
                padding: 20px;
                border-radius: 22px;
            }
        }
    
        .clinical-code-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .clinical-code-card,
        .icf-card {
            border: 1px solid #edf1f0;
            background: #ffffff;
            border-radius: 18px;
            padding: 16px;
        }

        .clinical-code-card .data-label,
        .icf-card .data-label {
            margin-bottom: 8px;
        }

        .icf-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .icf-card.wide {
            grid-column: 1 / -1;
        }

        .icf-section-card {
            margin-top: 18px;
            border: 1px solid #edf1f0;
            background: #fbfcfc;
            border-radius: 22px;
            padding: 18px;
        }

        .icf-section-title {
            margin: 0;
            color: #22343a;
            font-size: 18px;
            font-weight: 900;
        }

        .icf-section-subtitle {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.7;
        }

        @media (max-width: 900px) {
            .clinical-code-grid,
            .icf-grid {
                grid-template-columns: 1fr;
            }

            .icf-card.wide {
                grid-column: auto;
            }
        }

    
        .clinical-decision-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .clinical-decision-card {
            border: 1px solid #edf1f0;
            background: #ffffff;
            border-radius: 18px;
            padding: 16px;
            min-height: 118px;
        }

        .clinical-decision-card.wide {
            grid-column: 1 / -1;
            min-height: auto;
        }

        @media (max-width: 900px) {
            .clinical-decision-grid {
                grid-template-columns: 1fr;
            }

            .clinical-decision-card.wide {
                grid-column: auto;
            }
        }

    
        .supporting-attachment-card {
            border: 1px solid #dbeafe;
            background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
            border-radius: 16px;
            padding: 12px;
        }

        .supporting-file-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            background: #2f7c7a;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 900;
            box-shadow: 0 10px 24px rgba(47,124,122,.16);
        }

        .supporting-file-name {
            margin-top: 8px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

    
        .pain-tracking-card {
            border: 1px solid #e4efed;
            border-radius: 24px;
            padding: 18px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fffd 100%);
            margin-bottom: 18px;
        }

        .pain-tracking-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .pain-tracking-title {
            margin: 0;
            color: #22343a;
            font-size: 18px;
            font-weight: 900;
        }

        .pain-tracking-subtitle {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.7;
        }

        .pain-summary-pill {
            flex: 0 0 auto;
            border-radius: 999px;
            padding: 9px 12px;
            background: #f5f3ff;
            color: #7c3aed;
            font-size: 12px;
            font-weight: 900;
        }

        .pain-chart-grid {
            display: grid;
            gap: 12px;
        }

        .pain-chart-row {
            display: grid;
            grid-template-columns: 110px 1fr 54px;
            gap: 12px;
            align-items: center;
        }

        .pain-date {
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
        }

        .pain-bar-track {
            height: 14px;
            border-radius: 999px;
            background: #eef2f7;
            overflow: hidden;
        }

        .pain-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #2f7c7a, #8b5cf6);
            min-width: 4px;
        }

        .pain-score {
            color: #22343a;
            font-size: 13px;
            font-weight: 900;
            text-align: right;
        }

        .pain-note-list {
            margin-top: 14px;
            display: grid;
            gap: 8px;
        }

        .pain-note-item {
            border: 1px solid #eef2f1;
            border-radius: 16px;
            padding: 10px 12px;
            background: #ffffff;
            color: #475569;
            font-size: 12px;
            line-height: 1.6;
        }

        @media (max-width: 720px) {
            .pain-chart-row {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .pain-score {
                text-align: left;
            }

            .pain-tracking-head {
                flex-direction: column;
            }
        }

    
        .body-chart-view-card {
            border: 1px solid #e3efed;
            border-radius: 22px;
            padding: 16px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fffd 100%);
            margin-top: 14px;
        }

        .body-chart-view-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        @media (max-width: 920px) {
            .body-chart-view-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .body-chart-view-grid {
                grid-template-columns: 1fr;
            }
        }

    
        .pain-body-pro-card {
            margin-top: 16px;
            border: 1px solid #dbecea;
            border-radius: 24px;
            background: linear-gradient(135deg, #fbfffe 0%, #ffffff 55%, #f7fffc 100%);
            padding: 18px;
            box-shadow: 0 18px 45px rgba(31, 79, 77, .07);
        }

        .pain-body-pro-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .pain-body-pro-title {
            margin: 0;
            color: #1f4f4d;
            font-size: 18px;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .pain-body-pro-subtitle {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
            font-weight: 750;
        }

        .pain-body-pro-badge {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            background: #eefcf8;
            color: #1f4f4d;
            font-size: 11px;
            font-weight: 950;
            border: 1px solid #c9ebe4;
        }

        .pain-body-pro-areas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 12px;
            margin-top: 14px;
        }

        .pain-body-pro-area {
            border: 1px solid #dbecea;
            border-radius: 18px;
            background: #ffffff;
            padding: 14px;
            min-height: 108px;
        }

        .pain-body-pro-area-name {
            color: #20343a;
            font-size: 14px;
            font-weight: 950;
            margin-bottom: 10px;
            line-height: 1.35;
        }

        .pain-body-pro-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .pain-body-pro-pill {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 11px;
            font-weight: 900;
        }

        .pain-body-pro-pill.strong {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }

        .pain-body-pro-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 14px;
        }

        .pain-body-pro-note {
            border: 1px solid #dbecea;
            border-radius: 18px;
            background: #ffffff;
            padding: 14px;
        }

        .pain-body-pro-note.full {
            grid-column: 1 / -1;
        }

        .pain-body-pro-label {
            color: #64748b;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .pain-body-pro-value {
            color: #20343a;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 800;
            white-space: pre-line;
        }

        @media (max-width: 760px) {
            .pain-body-pro-head {
                flex-direction: column;
            }

            .pain-body-pro-grid {
                grid-template-columns: 1fr;
            }
        }

    
        .pain-body-pro-card.admin-full-width {
            grid-column: 1 / -1;
        }

    
        .goal-phase-view-card,
        .session-progress-view-card {
            margin-top: 16px;
            border: 1px solid #dbecea;
            border-radius: 24px;
            background: linear-gradient(135deg, #fbfffe 0%, #ffffff 58%, #f7fffc 100%);
            padding: 18px;
            box-shadow: 0 16px 38px rgba(31,79,77,.06);
        }

        .goal-phase-view-grid,
        .session-progress-view-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .goal-phase-view-item,
        .session-progress-view-item {
            border: 1px solid #dbecea;
            border-radius: 18px;
            background: #ffffff;
            padding: 14px;
        }

        .goal-phase-active {
            border-color: #8dd7cc;
            background: #f0fdfa;
        }

        .goal-phase-view-title,
        .session-progress-view-title {
            margin: 0;
            color: #1f4f4d;
            font-size: 18px;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        @media (max-width: 860px) {
            .goal-phase-view-grid,
            .session-progress-view-grid {
                grid-template-columns: 1fr;
            }
        }

    
        .rom-progress-view-card {
            margin-top: 16px;
            border: 1px solid #dbecea;
            border-radius: 24px;
            background: linear-gradient(135deg, #fbfffe 0%, #ffffff 58%, #f7fffc 100%);
            padding: 18px;
            box-shadow: 0 16px 38px rgba(31,79,77,.06);
        }

        .rom-progress-view-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .rom-progress-view-item {
            border: 1px solid #dbecea;
            border-radius: 18px;
            background: #ffffff;
            padding: 14px;
        }

        .rom-score-bar {
            height: 10px;
            border-radius: 999px;
            background: #eef2f7;
            overflow: hidden;
            margin-top: 10px;
        }

        .rom-score-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #2f8f8a, #22c55e);
        }

        @media (max-width: 900px) {
            .rom-progress-view-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

    
        /* Admin Medical Record Layout Polish */
        .content-grid.admin-clinical-full-layout {
            display: block;
        }

        .admin-patient-summary-strip {
            margin-bottom: 18px;
            border: 1px solid #dbecea;
            border-radius: 24px;
            background: rgba(255,255,255,.94);
            box-shadow: 0 18px 45px rgba(31,79,77,.08);
            padding: 18px;
        }

        .admin-patient-summary-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .admin-patient-summary-title {
            margin: 0;
            color: #1f4f4d;
            font-size: 20px;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .admin-patient-summary-subtitle {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
            font-weight: 750;
        }

        .admin-patient-summary-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
        }

        .admin-patient-summary-grid .profile-box {
            min-height: 96px;
        }

        .admin-clinical-main-wide > .section-card {
            width: 100%;
        }

        .goal-phase-view-card {
            grid-column: 1 / -1;
            width: 100%;
            padding: 22px;
            border-radius: 26px;
        }

        .goal-phase-view-card .data-label {
            line-height: 1.35;
        }

        .goal-phase-view-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 16px;
        }

        .goal-phase-view-item {
            min-height: 160px;
            padding: 18px;
            border-radius: 20px;
        }

        .goal-phase-view-item .data-value {
            margin-top: 10px;
            line-height: 1.7;
            word-break: normal;
            overflow-wrap: anywhere;
        }

        @media (max-width: 1100px) {
            .admin-patient-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .goal-phase-view-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .admin-patient-summary-grid {
                grid-template-columns: 1fr;
            }

            .admin-patient-summary-head {
                display: block;
            }
        }

    
        /* 5F Medical Record Font + Spacing Polish */
        body,
        button,
        input,
        select,
        textarea,
        table,
        label,
        .data-value,
        .data-label,
        .viewer-text,
        .section-subtitle,
        .form-section-subtitle,
        .patient-sub,
        .record-link,
        .report-link,
        .print-link {
            font-family: Arial, Helvetica, sans-serif !important;
        }

        input,
        select,
        textarea {
            font-size: 14px !important;
            line-height: 1.55 !important;
            letter-spacing: 0 !important;
        }

        textarea {
            min-height: 104px;
            resize: vertical;
        }

        label,
        .data-label {
            font-size: 12px !important;
            letter-spacing: .04em !important;
            font-weight: 900 !important;
        }

        .data-value,
        .viewer-text,
        .section-subtitle,
        .form-section-subtitle {
            font-size: 14px !important;
            line-height: 1.75 !important;
        }

        .section-card,
        .viewer-section,
        .clinical-decision-card,
        .data-box,
        .dry-needling-card,
        .goal-phase-card,
        .session-advanced-card,
        .rom-progress-card,
        .pain-body-chart-card {
            border-radius: 22px !important;
        }

        .form-grid,
        .clinical-decision-grid,
        .grid-2,
        .grid-3,
        .body-chart-grid {
            gap: 16px !important;
        }

        .section-card,
        .viewer-section {
            margin-bottom: 20px !important;
        }

        .dry-needling-card {
            padding: 20px !important;
            margin: 18px 0 !important;
        }

        .dry-needling-grid,
        .goal-phase-grid,
        .session-advanced-grid,
        .rom-progress-grid {
            gap: 16px !important;
        }

        .dry-needling-card select,
        .dry-needling-card input {
            height: 46px !important;
            border-radius: 14px !important;
        }

        @media (max-width: 900px) {
            .form-grid,
            .clinical-decision-grid,
            .grid-2,
            .grid-3,
            .dry-needling-grid,
            .goal-phase-grid,
            .session-advanced-grid,
            .rom-progress-grid {
                grid-template-columns: 1fr !important;
            }
        }

    </style>
    <!-- Khayra PWA -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#2f7c7a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Khayra ERM">
    <meta name="apple-mobile-web-app-title" content="Khayra ERM">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/images/khayra-logo.png">
</head>
<body>
@php
    $record = $visit->medicalRecord;

    $histories = $record && $record->histories && $record->histories->count()
        ? $record->histories
        : collect();

    $comorbidities = $record && $record->comorbidities && $record->comorbidities->count()
        ? $record->comorbidities
        : collect();

    $supportingData = $record && $record->supportingData && $record->supportingData->count()
        ? $record->supportingData
        : collect();

    $homeExercises = $record && $record->homeExercises && $record->homeExercises->count()
        ? $record->homeExercises
        : collect();

    $painTrackingRecords = $visit->patient
        ? $visit->patient->visits()
            ->with('medicalRecord')
            ->whereHas('medicalRecord', function ($query) {
                $query->whereNotNull('pain_scale');
            })
            ->orderBy('visit_date')
            ->get()
            ->map(function ($painVisit) {
                return (object) [
                    'visit_id' => $painVisit->id,
                    'visit_date' => $painVisit->visit_date,
                    'pain_scale' => optional($painVisit->medicalRecord)->pain_scale,
                    'pain_type' => optional($painVisit->medicalRecord)->pain_type,
                    'note' => optional($painVisit->medicalRecord)->pain_body_chart_note,
                ];
            })
        : collect();

    $painStart = optional($painTrackingRecords->first())->pain_scale;
    $painLatest = optional($painTrackingRecords->last())->pain_scale;
    $painDelta = (!is_null($painStart) && !is_null($painLatest)) ? ((int) $painLatest - (int) $painStart) : null;
    $adminPainBodyAreas = collect(json_decode($record->pain_body_areas ?? '[]', true) ?: []);

    if ($adminPainBodyAreas->isEmpty() && $record && !blank($record->pain_body_area)) {
        $adminPainBodyAreas = collect([[
            'area' => $record->pain_body_area,
            'intensity' => $record->pain_body_intensity,
            'type' => $record->pain_body_type,
        ]]);
    }

    $adminPainQualityTags = collect(json_decode($record->pain_quality_tags ?? '[]', true) ?: []);

@endphp

<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'visits'])

    <main class="main">
        <div class="topbar">
            <div>
                <h1 class="page-title">Medical Record Viewer</h1>
                <p class="page-subtitle">
                    Halaman ini dipakai admin untuk melihat hasil clinical assessment yang diisi therapist.
                    Admin bersifat monitoring, sedangkan pengisian rekam medis tetap menjadi domain therapist.
                </p>
            </div>
            <a href="/admin/visits" class="back-link">← Kembali ke Visits</a>
        </div>

        <section class="hero">
            <div class="hero-main">
                <div class="hero-badge">Viewer V2</div>
                <h2 class="hero-title">Clinical record untuk {{ $visit->patient->full_name ?? 'Patient' }}</h2>
                <p class="hero-text">
                    Admin dapat memantau isi rekam medis therapist secara lengkap untuk kebutuhan operasional,
                    follow up, dan koordinasi internal tanpa mengambil alih pengisian klinis.
                </p>

                <div class="hero-tags">
                    <span class="hero-tag">Visit #{{ $visit->id }}</span>
                    <span class="hero-tag">{{ $visit->visit_date ?: '-' }}</span>
                    <span class="hero-tag">{{ $visit->therapistRelation->full_name ?? $visit->therapist ?? '-' }}</span>
                </div>
            </div>

            <div class="hero-side">
                <h2 class="side-title">Ringkasan Visit</h2>
                <p class="side-subtitle">Informasi singkat visit yang sedang dipantau admin.</p>

                <div class="mini-grid">
                    <div class="mini-box">
                        <div class="mini-label">Patient</div>
                        <div class="mini-value">{{ $visit->patient->full_name ?? '-' }}</div>
                    </div>

                    <div class="mini-box">
                        <div class="mini-label">Visit Date</div>
                        <div class="mini-value">{{ $visit->visit_date ?: '-' }}</div>
                    </div>

                    <div class="mini-box">
                        <div class="mini-label">Status</div>
                        <div class="mini-value">
                            <span class="status-pill status-{{ $visit->status }}">{{ str_replace('_', ' ', $visit->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="notice">
            Viewer ini hanya untuk monitoring admin. Update clinical assessment dan report tetap dilakukan dari akun therapist.
        </div>

        <div class="content-grid admin-clinical-full-layout">
            <div class="admin-patient-summary-strip">
                <div class="admin-patient-summary-head">
                    <div>
                        <h2 class="admin-patient-summary-title">Patient Summary</h2>
                        <p class="admin-patient-summary-subtitle">Informasi dasar patient dan visit aktif sebelum admin membaca clinical notes.</p>
                    </div>
                    <a href="/admin/visits" class="btn btn-soft">All Visits</a>
                </div>

                <div class="admin-patient-summary-grid">
                    <div class="profile-box">
                        <div class="profile-label">Patient</div>
                        <div class="profile-value">{{ $visit->patient->full_name ?? '-' }}</div>
                    </div>

                    <div class="profile-box">
                        <div class="profile-label">Visit Date</div>
                        <div class="profile-value">{{ $visit->visit_date ?: '-' }}</div>
                    </div>

                    <div class="profile-box">
                        <div class="profile-label">Therapist</div>
                        <div class="profile-value">{{ $visit->therapistRelation->full_name ?? $visit->therapist ?? '-' }}</div>
                    </div>

                    <div class="profile-box">
                        <div class="profile-label">Status</div>
                        <div class="profile-value">{{ $visit->status ?: '-' }}</div>
                    </div>

                    <div class="profile-box">
                        <div class="profile-label">Visit Notes</div>
                        <div class="profile-value">{{ $visit->notes ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="admin-clinical-main-wide">
                <section class="section-card">
                <h2 class="section-title">Clinical Notes V2</h2>
                <p class="section-subtitle">
                    Tampilan lengkap hasil clinical assessment therapist untuk monitoring, koordinasi, dan follow-up admin.
                </p>


                <div class="pain-tracking-card">
                    <div class="pain-tracking-head">
                        <div>
                            <h3 class="pain-tracking-title">Pain Tracking per Session</h3>
                            <p class="pain-tracking-subtitle">Grafik sederhana pain scale 0-10 dari setiap visit pasien yang sudah punya rekam medis.</p>
                        </div>

                        <div class="pain-summary-pill">
                            @if(is_null($painDelta))
                                Trend -
                            @elseif($painDelta < 0)
                                Turun {{ abs($painDelta) }} poin
                            @elseif($painDelta > 0)
                                Naik {{ $painDelta }} poin
                            @else
                                Stabil
                            @endif
                        </div>
                    </div>

                    @if($painTrackingRecords->count())
                        <div class="pain-chart-grid">
                            @foreach($painTrackingRecords as $painItem)
                                @php
                                    $painScore = is_null($painItem->pain_scale) ? 0 : min(max((int) $painItem->pain_scale, 0), 10);
                                    $painPercent = $painScore * 10;
                                @endphp

                                <div class="pain-chart-row">
                                    <div class="pain-date">{{ $painItem->visit_date ?: '-' }}</div>
                                    <div class="pain-bar-track">
                                        <div class="pain-bar-fill" style="width: {{ $painPercent }}%;"></div>
                                    </div>
                                    <div class="pain-score">{{ $painScore }}/10</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pain-note-list">
                            @foreach($painTrackingRecords->take(-3) as $painItem)
                                @if($painItem->pain_type || $painItem->note)
                                    <div class="pain-note-item">
                                        <strong>{{ $painItem->visit_date ?: '-' }}</strong>
                                        · Type: {{ $painItem->pain_type ?: '-' }}
                                        @if($painItem->note)
                                            <br>{{ $painItem->note }}
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="empty-block">Belum ada pain scale antar sesi.</div>
                    @endif
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">1. Chief Complaint & Pain Profile</h3>
                    <p class="viewer-text">Keluhan utama, onset, nyeri, dan keterbatasan fungsi awal.</p>

                    <div class="grid-2">
                        <div class="data-box">
                            <div class="data-label">Complaint</div>
                            <div class="data-value">{{ $record->complaint ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Onset</div>
                            <div class="data-value">{{ $record->onset ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-3 spacer">
                        <div class="data-box">
                            <div class="data-label">Pain Scale</div>
                            <div class="data-value">{{ $record->pain_scale ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Pain Type</div>
                            <div class="data-value">{{ $record->pain_type ?? '-' }}</div>
                        </div>

                    </div>

                    <div class="grid-2 spacer">
                        <div class="data-box">
                            <div class="data-label">Condition Felt</div>
                            <div class="data-value">{{ $record->condition_felt ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Pain Body Chart Note</div>
                            <div class="data-value">{{ $record->pain_body_chart_note ?? '-' }}</div>
                        </div>

                    <div class="pain-body-pro-card admin-full-width">
                        <div class="pain-body-pro-head">
                            <div>
                                <h4 class="pain-body-pro-title">Pain Body Chart</h4>
                                <p class="pain-body-pro-subtitle">Ringkasan area nyeri, intensitas per area, tipe nyeri, trigger, relief, dan catatan klinis.</p>
                            </div>
                            <div class="pain-body-pro-badge">Pro Anatomy Map</div>
                        </div>

                        @if($adminPainBodyAreas->count())
                            <div class="pain-body-pro-areas">
                                @foreach($adminPainBodyAreas as $painArea)
                                    @php
                                        $painAreaName = is_array($painArea) ? ($painArea['area'] ?? '-') : $painArea;
                                        $painAreaIntensity = is_array($painArea) ? ($painArea['intensity'] ?? null) : null;
                                        $painAreaType = is_array($painArea) ? ($painArea['type'] ?? null) : null;
                                    @endphp

                                    <div class="pain-body-pro-area">
                                        <div class="pain-body-pro-area-name">{{ $painAreaName ?: '-' }}</div>
                                        <div class="pain-body-pro-meta">
                                            <span class="pain-body-pro-pill strong">Intensity {{ $painAreaIntensity !== null && $painAreaIntensity !== '' ? $painAreaIntensity . '/10' : '-' }}</span>
                                            <span class="pain-body-pro-pill">Type {{ $painAreaType ?: '-' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-block">Belum ada area nyeri terstruktur.</div>
                        @endif

                        @if($adminPainQualityTags->count())
                            <div class="pain-body-pro-note full" style="margin-top:14px;">
                                <div class="pain-body-pro-label">Pain Quality Tags</div>
                                <div class="pain-body-pro-meta">
                                    @foreach($adminPainQualityTags as $qualityTag)
                                        <span class="pain-body-pro-pill">{{ $qualityTag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pain-body-pro-grid">
                            <div class="pain-body-pro-note">
                                <div class="pain-body-pro-label">Trigger / Memburuk Saat</div>
                                <div class="pain-body-pro-value">{{ $record->pain_aggravating_activity ?? '-' }}</div>
                            </div>

                            <div class="pain-body-pro-note">
                                <div class="pain-body-pro-label">Relief / Membaik Saat</div>
                                <div class="pain-body-pro-value">{{ $record->pain_easing_activity ?? '-' }}</div>
                            </div>

                            <div class="pain-body-pro-note full">
                                <div class="pain-body-pro-label">Catatan Nyeri</div>
                                <div class="pain-body-pro-value">{{ $record->pain_body_chart_note ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid-2 spacer">
                        <div class="data-box">
                            <div class="data-label">Easing Factors</div>
                            <div class="data-value">{{ $record->easing_factors ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Aggravating Factors</div>
                            <div class="data-value">{{ $record->aggravating_factors ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="data-box spacer">
                        <div class="data-label">Special Test Notes</div>
                        <div class="data-value">{{ $record->special_test_notes ?? '-' }}</div>
                    </div>
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">7. Diagnosa & Clinical Decision</h3>
                    <p class="viewer-text">Diagnosa, ICD, goal, referral, dan rencana program.</p>

                    <div class="data-box">
                        <div class="data-label">Diagnosa</div>
                        <div class="data-value">{{ $record->physiotherapy_diagnosis ?? '-' }}</div>

                        <div class="clinical-code-grid">
                            <div class="clinical-code-card">
                                <div class="data-label">ICD Code</div>
                                <div class="data-value">{{ $record->icd_code ?? '-' }}</div>
                            </div>

                            <div class="clinical-code-card">
                                <div class="data-label">ICD Diagnosis</div>
                                <div class="data-value">{{ $record->icd_diagnosis ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="clinical-decision-grid">
                        <div class="clinical-decision-card">
                        </div>

                        <div class="clinical-decision-card">
                            <div class="data-label">Functional Limitation (Clinical)</div>
                            <div class="data-value">{{ $record->functional_limitation_clinical ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="icf-section-card">
                        <h4 class="icf-section-title">Clinical Reasoning</h4>
                        <p class="icf-section-subtitle">Kerangka assessment berdasarkan Catatan clinical reasoning tambahan.</p>

                        <div class="icf-grid">
                            <div class="icf-card">
                                <div class="data-label">Body Function</div>
                                <div class="data-value">{{ $record->icf_body_function ?? '-' }}</div>
                            </div>

                            <div class="icf-card">
                                <div class="data-label">Body Structure</div>
                                <div class="data-value">{{ $record->icf_body_structure ?? '-' }}</div>
                            </div>

                            <div class="icf-card">
                                <div class="data-label">Activities & Participation</div>
                                <div class="data-value">{{ $record->icf_activities_participation ?? '-' }}</div>
                            </div>

                            <div class="icf-card">
                                <div class="data-label">Personal Factors</div>
                                <div class="data-value">{{ $record->icf_personal_factors ?? '-' }}</div>
                            </div>

                            <div class="icf-card wide">
                                <div class="data-label">Environmental Factors</div>
                                <div class="data-value">{{ $record->icf_environmental_factors ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="clinical-decision-grid">
                        <div class="clinical-decision-card">
                            <div class="data-label">Patient Goal</div>
                            <div class="data-value">{{ $record->patient_goal ?? '-' }}</div>
                        </div>

                    <div class="goal-phase-view-card">
                        <h4 class="goal-phase-view-title">Treatment Goal Phase</h4>
                        <div class="data-label" style="margin-top:8px;">Current Phase</div>
                        <div class="data-value">{{ $record->goal_phase ?? '-' }}</div>

                        <div class="goal-phase-view-grid">
                            <div class="goal-phase-view-item {{ ($record->goal_phase ?? '') === 'Phase 1 - Pain Control' ? 'goal-phase-active' : '' }}">
                                <div class="data-label">Phase 1 - Pain Control</div>
                                <div class="data-value">{{ $record->phase_1_goal ?? '-' }}</div>
                            </div>

                            <div class="goal-phase-view-item {{ ($record->goal_phase ?? '') === 'Phase 2 - Mobility / Strength' ? 'goal-phase-active' : '' }}">
                                <div class="data-label">Phase 2 - Mobility / Strength</div>
                                <div class="data-value">{{ $record->phase_2_goal ?? '-' }}</div>
                            </div>

                            <div class="goal-phase-view-item {{ ($record->goal_phase ?? '') === 'Phase 3 - Functional Return' ? 'goal-phase-active' : '' }}">
                                <div class="data-label">Phase 3 - Functional Return</div>
                                <div class="data-value">{{ $record->phase_3_goal ?? '-' }}</div>
                            </div>
                        </div>
                    </div>


                        <div class="clinical-decision-card">
                            <div class="data-label">Referral</div>
                            <div class="data-value">{{ $record->referral ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="clinical-decision-grid">
                        <div class="clinical-decision-card wide">
                            <div class="data-label">Program Patient</div>
                            <div class="data-value">{{ $record->program_patient ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-3 spacer">
                        <div class="data-box">
                            <div class="data-label">Date of Control</div>
                            <div class="data-value">{{ optional($record->date_of_control)->format('Y-m-d') ?: '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Total Session</div>
                            <div class="data-value">{{ $record->total_session ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Frequency per Week</div>
                            <div class="data-value">{{ $record->frequency_per_week ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="data-box spacer">
                        <div class="data-label">Control Plan</div>
                        <div class="data-value">{{ $record->control_plan ?? '-' }}</div>
                    </div>
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">8. Health Management</h3>
                    <p class="viewer-text">Catatan edukasi dan manajemen pendukung terapi.</p>

                    <div class="grid-3">
                        <div class="data-box">
                            <div class="data-label">Diet / Nutrition</div>
                            <div class="data-value">{{ $record->diet_nutrition ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Lifestyle</div>
                            <div class="data-value">{{ $record->lifestyle ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Flare-up Management</div>
                            <div class="data-value">{{ $record->flare_up_management ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">9. Home Exercise Program</h3>
                    <p class="viewer-text">Latihan rumah yang diberikan therapist kepada patient.</p>

                    @if($homeExercises->count())
                        <div class="repeat-wrap">
                            @foreach($homeExercises as $exercise)
                                <div class="repeat-item">
                                    <div class="grid-3">
                                        <div class="data-box">
                                            <div class="data-label">Exercise</div>
                                            <div class="data-value">{{ $exercise->exercise ?? '-' }}</div>
                                        </div>

                                        <div class="data-box">
                                            <div class="data-label">Dosage</div>
                                            <div class="data-value">{{ $exercise->dosage ?? '-' }}</div>
                                        </div>

                                        <div class="data-box">
                                            <div class="data-label">Note / Caution</div>
                                            <div class="data-value">{{ $exercise->note_caution ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-block">Belum ada home exercise program.</div>
                    @endif
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">10. Session Progress</h3>
                    <p class="viewer-text">Ringkasan progress dan intervensi sesi yang dicatat therapist.</p>

                    <div class="grid-2">
                        <div class="data-box">
                            <div class="data-label">Assessment (Legacy)</div>
                            <div class="data-value">{{ $record->assessment ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Treatment (Legacy)</div>
                            <div class="data-value">{{ $record->treatment ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-2 spacer">
                        <div class="data-box">
                            <div class="data-label">Treatment Given</div>
                            <div class="data-value">{{ $record->treatment_given ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Progress Note</div>
                            <div class="data-value">{{ $record->progress_note ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-2 spacer">
                        <div class="data-box">
                            <div class="data-label">Response to Treatment</div>
                            <div class="data-value">{{ $record->response_to_treatment ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Recommendation</div>
                            <div class="data-value">{{ $record->recommendation ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="data-box spacer">
                        <div class="data-label">Next Session Plan</div>
                        <div class="data-value">{{ $record->next_session_plan ?? '-' }}</div>
                    </div>


                    <div class="rom-progress-view-card">
                        <h4 class="session-progress-view-title">ROM / Functional Progress Chart</h4>
                        <div class="rom-progress-view-grid">
                            <div class="rom-progress-view-item">
                                <div class="data-label">ROM Kanan</div>
                                <div class="data-value">{{ $record->rom_cervical_rotation ?? '-' }}</div>
                            </div>

                            <div class="rom-progress-view-item">
                                <div class="data-label">ROM Kiri</div>
                                <div class="data-value">{{ $record->rom_shoulder_elevation ?? '-' }}</div>
                            </div>

                            <div class="rom-progress-view-item">
                                <div class="data-label">Functional Score</div>
                                <div class="data-value">{{ is_null($record->functional_score) ? '-' : $record->functional_score . '%' }}</div>
                                @if(!is_null($record->functional_score))
                                    <div class="rom-score-bar">
                                        <div class="rom-score-fill" style="width: {{ min(max((int) $record->functional_score, 0), 100) }}%;"></div>
                                    </div>
                                @endif
                            </div>

                            <div class="rom-progress-view-item">
                                <div class="data-label">Activity Tolerance</div>
                                <div class="data-value">{{ $record->activity_tolerance ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="session-progress-view-card">
                        <h4 class="session-progress-view-title">Session Progress Lanjutan</h4>
                        <div class="session-progress-view-grid">
                            <div class="session-progress-view-item">
                                <div class="data-label">Session Focus</div>
                                <div class="data-value">{{ $record->session_focus ?? '-' }}</div>
                            </div>

                            <div class="session-progress-view-item">
                                <div class="data-label">Session Progress Note</div>
                                <div class="data-value">{{ $record->session_progress_note ?? '-' }}</div>
                            </div>

                            <div class="session-progress-view-item">
                                <div class="data-label">Pain After Session</div>
                                <div class="data-value">{{ is_null($record->session_pain_after) ? '-' : $record->session_pain_after . '/10' }}</div>
                            </div>

                            <div class="session-progress-view-item">
                                <div class="data-label">Homework Status</div>
                                <div class="data-value">{{ $record->session_homework_status ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            </div>
        </div>
    </main>
</div>
<script>
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    var deferredInstallPrompt = null;

    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        deferredInstallPrompt = event;

        if (document.getElementById('khayraInstallAppButton')) {
            return;
        }

        var button = document.createElement('button');
        button.id = 'khayraInstallAppButton';
        button.type = 'button';
        button.innerText = 'Install App';
        button.style.position = 'fixed';
        button.style.right = '18px';
        button.style.bottom = '18px';
        button.style.zIndex = '99999';
        button.style.border = '0';
        button.style.borderRadius = '999px';
        button.style.padding = '12px 16px';
        button.style.background = '#2f7c7a';
        button.style.color = '#ffffff';
        button.style.fontWeight = '900';
        button.style.fontFamily = 'Arial, sans-serif';
        button.style.fontSize = '13px';
        button.style.boxShadow = '0 14px 30px rgba(47,124,122,.24)';
        button.style.cursor = 'pointer';

        button.addEventListener('click', function () {
            if (!deferredInstallPrompt) {
                return;
            }

            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.finally(function () {
                deferredInstallPrompt = null;
                button.remove();
            });
        });

        document.body.appendChild(button);
    });
})();
</script>

</body>
</html>