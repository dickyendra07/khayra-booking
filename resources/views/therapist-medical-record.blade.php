<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Record V2 - Khayra Physio</title>
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

        .page {
            min-height: 100vh;
            padding: 24px 20px 40px;
        }

        .container {
            max-width: 1380px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .brand {
            font-size: 28px;
            font-weight: 800;
            color: #0f766e;
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ghost-link {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 12px;
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
            font-size: 14px;
            font-weight: 700;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.35fr .85fr;
            gap: 18px;
            margin-bottom: 20px;
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
            font-size: 38px;
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

        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 18px;
            border: 1px solid transparent;
            font-size: 14px;
            line-height: 1.7;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fecaca;
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

        .profile-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .profile-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .profile-value {
            font-size: 15px;
            color: #111827;
            line-height: 1.6;
            word-break: break-word;
        }

        .form-section {
            margin-bottom: 24px;
            padding-bottom: 22px;
            border-bottom: 1px solid #e8f1ef;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-section-title {
            margin: 0 0 8px;
            font-size: 20px;
            color: #0f766e;
            font-weight: 800;
        }

        .form-section-text {
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

        .field {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }

        label {
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #374151;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #d7dedd;
            border-radius: 14px;
            font-size: 14px;
            background: #fcfefd;
            color: #111827;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
            line-height: 1.7;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #0f766e;
            box-shadow: 0 0 0 4px rgba(15,118,110,.08);
        }

        .helper {
            margin-top: 7px;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.6;
        }

        .inline-card {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .inline-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .inline-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f766e;
        }

        .tiny-btn,
        .remove-btn,
        .submit-btn {
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 800;
        }

        .tiny-btn {
            padding: 10px 14px;
            background: #0f766e;
            color: white;
            font-size: 13px;
            margin-top: 6px;
        }

        .remove-btn {
            padding: 8px 10px;
            background: #111827;
            color: white;
            font-size: 12px;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 48px;
            padding: 0 2px;
            font-size: 14px;
            color: #374151;
            font-weight: 700;
        }

        .checkbox-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        .submit-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .submit-btn {
            padding: 15px 22px;
            background: #0f766e;
            color: white;
            font-size: 15px;
            box-shadow: 0 12px 26px rgba(15,118,110,.16);
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

        @media (max-width: 768px) {
            .page {
                padding: 16px 14px 32px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero-title {
                font-size: 30px;
            }

            .hero-main,
            .hero-side,
            .section-card {
                padding: 20px;
                border-radius: 22px;
            }

            .brand {
                font-size: 24px;
            }
        }
    
        .clinical-summary-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 18px;
            margin-bottom: 20px;
        }

        .clinical-summary-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .clinical-summary-title {
            margin: 0;
            font-size: 24px;
            color: #0f766e;
            font-weight: 900;
        }

        .clinical-summary-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .summary-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .summary-metric {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 15px;
        }

        .summary-label {
            font-size: 11px;
            font-weight: 800;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .45px;
            margin-bottom: 7px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 900;
            color: #111827;
            line-height: 1.45;
            word-break: break-word;
        }

        .completion-wrap {
            margin-top: 12px;
        }

        .completion-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 9px;
            font-size: 13px;
            color: #374151;
            font-weight: 800;
        }

        .completion-bar {
            height: 14px;
            border-radius: 999px;
            background: #e8f1ef;
            overflow: hidden;
        }

        .completion-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(135deg, #0f766e 0%, #2f7f74 100%);
        }

        .pain-meter {
            margin-top: 12px;
        }

        .pain-scale-row {
            display: flex;
            justify-content: space-between;
            gap: 6px;
            margin-top: 7px;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 800;
        }

        .pain-bar {
            height: 16px;
            border-radius: 999px;
            background: linear-gradient(90deg, #dcfce7 0%, #fef3c7 50%, #fee2e2 100%);
            overflow: hidden;
            border: 1px solid #e5efec;
        }

        .pain-indicator {
            height: 100%;
            background: rgba(17,24,39,.20);
            border-radius: 999px;
        }

        .issue-list {
            display: grid;
            gap: 10px;
        }

        .issue-item {
            padding: 12px 14px;
            border-radius: 16px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 13px;
            line-height: 1.6;
            font-weight: 800;
        }

        .issue-ok {
            padding: 14px 16px;
            border-radius: 16px;
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #166534;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 800;
        }

        .clinical-action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .primary-action,
        .soft-action,
        .blue-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 13px;
            font-size: 13px;
            font-weight: 900;
            border: 1px solid transparent;
        }

        .primary-action {
            background: #0f766e;
            color: #ffffff;
            box-shadow: 0 12px 26px rgba(15,118,110,.16);
        }

        .soft-action {
            background: #ffffff;
            color: #0f766e;
            border-color: #d7ebe6;
        }

        .blue-action {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #cfe0ff;
        }

        @media (max-width: 1180px) {
            .clinical-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .summary-metrics {
                grid-template-columns: 1fr;
            }
        }

    
        .mini-section-title {
            margin: 18px 0 6px;
            font-size: 16px;
            font-weight: 900;
            color: #22343a;
        }
        .form-subsection {
            margin-top: 18px;
            padding: 18px;
            border: 1px solid #edf1f0;
            border-radius: 22px;
            background: #fbfcfc;
        }

    
        .file-upload-box {
            border: 1px dashed #b9d8d4;
            background: linear-gradient(135deg, #f8fffe 0%, #ffffff 100%);
            border-radius: 18px;
            padding: 14px;
            display: grid;
            gap: 8px;
        }

        .file-upload-box input[type="file"] {
            width: 100%;
            border: 1px solid #dbe7e5;
            border-radius: 12px;
            padding: 10px;
            background: #fff;
            font-size: 13px;
            color: #334155;
        }

        .file-upload-hint {
            font-size: 12px;
            line-height: 1.45;
            color: #64748b;
        }

        .current-file-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            max-width: 100%;
            margin-top: 4px;
            padding: 8px 10px;
            border-radius: 999px;
            background: #eefaf7;
            border: 1px solid #cfe9e3;
            color: #176f69;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            overflow-wrap: anywhere;
        }

        .supporting-row .grid-3 {
            align-items: start;
        }

    
        .pain-history-card {
            border: 1px solid #dbecea;
            border-radius: 28px;
            padding: 22px;
            background: linear-gradient(135deg, #ffffff 0%, #f7fffd 100%);
            box-shadow: 0 18px 45px rgba(31, 79, 77, .08);
            margin: 22px 0;
        }

        .pain-history-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 18px;
        }

        .pain-history-eyebrow {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 0 12px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #0f766e;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .55px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .pain-history-title {
            margin: 0;
            color: #20343a;
            font-size: 24px;
            font-weight: 900;
            line-height: 1.15;
        }

        .pain-history-subtitle {
            margin: 8px 0 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.7;
            max-width: 720px;
        }

        .pain-history-trend {
            flex: 0 0 auto;
            border-radius: 999px;
            padding: 10px 14px;
            background: #f5f3ff;
            color: #7c3aed;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .pain-history-chart {
            display: grid;
            gap: 12px;
        }

        .pain-history-row {
            display: grid;
            grid-template-columns: 120px 1fr 64px;
            gap: 14px;
            align-items: center;
            padding: 12px;
            border: 1px solid #edf4f2;
            border-radius: 18px;
            background: #ffffff;
        }

        .pain-history-date {
            color: #20343a;
            font-size: 13px;
            font-weight: 900;
            line-height: 1.35;
        }

        .pain-history-date span {
            display: block;
            color: #94a3b8;
            font-size: 10px;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .pain-history-track {
            height: 16px;
            border-radius: 999px;
            background: #eef2f7;
            overflow: hidden;
        }

        .pain-history-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #2f7c7a 0%, #f59e0b 62%, #ef4444 100%);
            min-width: 6px;
        }

        .pain-history-score {
            color: #20343a;
            font-size: 14px;
            font-weight: 900;
            text-align: right;
        }

        .pain-history-footer {
            margin-top: 14px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .pain-history-note,
        .pain-history-empty {
            border: 1px solid #edf4f2;
            border-radius: 18px;
            padding: 13px 14px;
            background: #ffffff;
            color: #475569;
            font-size: 13px;
            line-height: 1.7;
        }

        .pain-history-note strong {
            color: #20343a;
        }

        @media (max-width: 720px) {
            .pain-history-header {
                flex-direction: column;
            }

            .pain-history-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .pain-history-score {
                text-align: left;
            }

            .pain-history-footer {
                grid-template-columns: 1fr;
            }
        }

    
        .body-chart-card {
            border: 1px solid #dbecea;
            border-radius: 30px;
            padding: 22px;
            margin-top: 18px;
            background:
                radial-gradient(circle at 12% 8%, rgba(47, 124, 122, .10), transparent 28%),
                radial-gradient(circle at 90% 12%, rgba(124, 58, 237, .07), transparent 26%),
                linear-gradient(135deg, #ffffff 0%, #f8fffd 100%);
            box-shadow: 0 18px 44px rgba(31, 79, 77, .08);
            font-family: inherit;
        }

        .body-chart-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .body-chart-title {
            margin: 0;
            color: #1f4f4d;
            font-size: 22px;
            font-weight: 950;
            line-height: 1.18;
            letter-spacing: -0.035em;
        }

        .body-chart-subtitle {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.75;
            font-weight: 750;
        }

        .body-chart-badge {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            border-radius: 999px;
            padding: 0 14px;
            background: #eefcf8;
            color: #087c68;
            font-size: 12px;
            font-weight: 950;
            white-space: nowrap;
            border: 1px solid #c9f1e6;
        }

        .body-map-wrap {
            display: grid;
            grid-template-columns: 440px minmax(0, 1fr);
            gap: 20px;
            align-items: start;
        }

        .anatomy-map-shell {
            border: 1px solid #dce7e5;
            border-radius: 26px;
            background: rgba(255,255,255,.92);
            padding: 16px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.78);
        }

        .anatomy-map-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .anatomy-map-title {
            color: #20343a;
            font-size: 13px;
            font-weight: 950;
            letter-spacing: .01em;
        }

        .anatomy-map-pill {
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            background: #f1f7f6;
            border: 1px solid #dbecea;
            border-radius: 999px;
            padding: 6px 10px;
            white-space: nowrap;
        }

        .anatomy-side-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
        }

        .anatomy-side-tab {
            border: 1px solid #dce7e5;
            background: #ffffff;
            color: #1f4f4d;
            border-radius: 999px;
            min-height: 36px;
            font-size: 12px;
            font-weight: 950;
            cursor: pointer;
            font-family: inherit;
            transition: .18s ease;
        }

        .anatomy-side-tab.active,
        .anatomy-side-tab:hover {
            background: #2f7c7a;
            border-color: #2f7c7a;
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(47,124,122,.18);
        }

        .anatomy-svg-wrap {
            position: relative;
            min-height: 500px;
            border-radius: 24px;
            border: 1px dashed #b7d6d2;
            background:
                radial-gradient(circle at center top, rgba(47,124,122,.08), transparent 42%),
                linear-gradient(180deg, #ffffff 0%, #eef8f6 100%);
            overflow: hidden;
        }

        .anatomy-svg-panel {
            position: absolute;
            inset: 0;
            padding: 12px;
            opacity: 0;
            pointer-events: none;
            transform: translateY(6px);
            transition: .18s ease;
        }

        .anatomy-svg-panel.active {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .anatomy-svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .anatomy-base {
            fill: #dcefed;
            stroke: #a9ccc7;
            stroke-width: 1.6;
        }

        .anatomy-detail {
            fill: none;
            stroke: rgba(47,124,122,.23);
            stroke-width: 1.2;
            stroke-linecap: round;
        }

        .anatomy-region {
            pointer-events: all;
            fill: rgba(47,124,122,.12);
            stroke: rgba(47,124,122,.32);
            stroke-width: 1.4;
            cursor: pointer;
            transition: .18s ease;
        }

        .anatomy-region:hover,
        .anatomy-region.active {
            fill: rgba(47,124,122,.44);
            stroke: #1f4f4d;
            stroke-width: 2;
            filter: drop-shadow(0 8px 12px rgba(31,79,77,.18));
        }

        .anatomy-label {
            pointer-events: none;
            fill: #1f4f4d;
            font-size: 9px;
            font-weight: 900;
            font-family: inherit;
            text-anchor: middle;
        }

        .anatomy-label.light {
            fill: #ffffff;
        }

        .body-map-helper {
            margin-top: 12px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.65;
            font-weight: 750;
        }

        .body-chart-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .body-chart-grid .field {
            margin: 0;
        }

        .body-chart-grid .field.full {
            grid-column: 1 / -1;
        }

        .quick-side-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0 14px;
        }

        .quick-side-btn {
            border: 1px solid #dce7e5;
            background: #ffffff;
            color: #1f4f4d;
            border-radius: 999px;
            min-height: 32px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
            font-family: inherit;
            transition: .18s ease;
        }

        .quick-side-btn:hover,
        .quick-side-btn.active {
            background: #eefcf8;
            border-color: #9bd8cc;
            box-shadow: 0 8px 18px rgba(31,79,77,.08);
        }

        .body-chart-card label {
            display: block;
            margin-bottom: 8px;
            color: #20343a;
            font-size: 13px;
            font-weight: 950;
            font-family: inherit;
        }

        .body-chart-card select,
        .body-chart-card textarea {
            width: 100%;
            border: 1px solid #dce7e5;
            border-radius: 16px;
            padding: 13px 14px;
            color: #20343a;
            background: #ffffff;
            font-size: 14px;
            font-family: inherit;
            font-weight: 800;
            outline: none;
            transition: .18s ease;
        }

        .body-chart-card textarea {
            min-height: 132px;
            resize: vertical;
            line-height: 1.65;
        }

        .body-chart-card select:focus,
        .body-chart-card textarea:focus {
            border-color: #2f7c7a;
            box-shadow: 0 0 0 4px rgba(47, 124, 122, .10);
        }

        .body-region-preview {
            border: 1px dashed #b7d6d2;
            border-radius: 18px;
            padding: 13px 14px;
            background: #f8fffd;
            color: #48636a;
            font-size: 13px;
            line-height: 1.75;
            margin-top: 14px;
            font-weight: 750;
        }

        .body-region-preview strong {
            color: #20343a;
            font-weight: 950;
        }

        @media (max-width: 1180px) {
            .body-map-wrap {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 920px) {
            .body-chart-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .body-chart-head {
                flex-direction: column;
            }

            .body-chart-grid {
                grid-template-columns: 1fr;
            }

            .anatomy-svg-wrap {
                min-height: 460px;
            }
        }

    
        .selected-area-card {
            margin-top: 12px;
            border: 1px solid #dbecea;
            border-radius: 18px;
            padding: 12px;
            background: #ffffff;
        }

        .selected-area-title {
            color: #20343a;
            font-size: 12px;
            font-weight: 950;
            margin-bottom: 9px;
        }

        .selected-area-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            min-height: 32px;
        }

        .selected-area-chip {
            border: 1px solid #9bd8cc;
            background: #eefcf8;
            color: #1f4f4d;
            border-radius: 999px;
            min-height: 30px;
            padding: 0 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 900;
        }

        .selected-area-chip button {
            border: 0;
            background: transparent;
            color: #ef4444;
            cursor: pointer;
            font-size: 14px;
            font-weight: 950;
            padding: 0;
            line-height: 1;
        }

        .pain-quality-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .pain-quality-check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #dce7e5;
            background: #ffffff;
            color: #20343a;
            border-radius: 999px;
            min-height: 34px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
        }

        .pain-quality-check input {
            width: auto;
            margin: 0;
            accent-color: #2f7c7a;
        }

        .pain-quality-check:has(input:checked) {
            background: #eefcf8;
            border-color: #9bd8cc;
            color: #1f4f4d;
        }

    
        .pain-area-hidden-field {
            display: none;
        }

        .pain-guidance-card {
            border: 1px solid #dbecea;
            background: #f8fffd;
            color: #48636a;
            border-radius: 18px;
            padding: 12px 14px;
            font-size: 13px;
            line-height: 1.7;
            font-weight: 750;
            margin-bottom: 14px;
        }

        .pain-guidance-card strong {
            color: #1f4f4d;
            font-weight: 950;
        }

        .pain-advanced {
            grid-column: 1 / -1;
            border: 1px solid #dbecea;
            border-radius: 18px;
            background: #ffffff;
            padding: 0;
            overflow: hidden;
        }

        .pain-advanced summary {
            cursor: pointer;
            list-style: none;
            padding: 14px 16px;
            color: #1f4f4d;
            font-size: 13px;
            font-weight: 950;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pain-advanced summary::-webkit-details-marker {
            display: none;
        }

        .pain-advanced summary::after {
            content: '+';
            width: 26px;
            height: 26px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eefcf8;
            color: #1f4f4d;
            font-weight: 950;
        }

        .pain-advanced[open] summary::after {
            content: '-';
        }

        .pain-advanced-inner {
            border-top: 1px solid #eef3f2;
            padding: 14px 16px 16px;
        }

        .pain-main-fields {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 14px;
        }

        @media (max-width: 920px) {
            .pain-main-fields {
                grid-template-columns: 1fr;
            }
        }

    
        .pain-area-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pain-area-row {
            border: 1px solid #dbecea;
            border-radius: 18px;
            background: #ffffff;
            padding: 12px;
            display: grid;
            grid-template-columns: 1fr 120px 34px;
            gap: 10px;
            align-items: center;
        }

        .pain-area-name {
            color: #20343a;
            font-size: 13px;
            font-weight: 950;
            line-height: 1.35;
        }

        .pain-area-sub {
            margin-top: 3px;
            color: #64748b;
            font-size: 11px;
            font-weight: 750;
        }

        .pain-area-intensity {
            width: 100%;
            min-height: 38px;
            border: 1px solid #dce7e5;
            border-radius: 14px;
            padding: 0 10px;
            color: #20343a;
            background: #f8fffd;
            font-size: 13px;
            font-weight: 900;
            font-family: inherit;
            outline: none;
        }

        .pain-area-remove {
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 999px;
            background: #fff1f2;
            color: #e11d48;
            font-size: 16px;
            font-weight: 950;
            cursor: pointer;
            font-family: inherit;
        }

        .pain-area-empty {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.6;
            border: 1px dashed #dbecea;
            border-radius: 16px;
            padding: 12px;
            background: #fbfffe;
        }

        .pain-global-intensity-hidden {
            display: none;
        }

        @media (max-width: 640px) {
            .pain-area-row {
                grid-template-columns: 1fr;
            }

            .pain-area-remove {
                width: 100%;
            }
        }

    
        .pain-side-hidden-field,
        .pain-type-hidden-field {
            display: none;
        }

        .pain-area-row {
            grid-template-columns: minmax(0, 1fr) 118px 150px 34px;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border-radius: 20px;
            box-shadow: 0 10px 24px rgba(31, 79, 77, .04);
        }

        .pain-area-name {
            font-size: 14px;
            letter-spacing: -.01em;
        }

        .pain-area-sub {
            font-size: 11px;
            line-height: 1.45;
        }

        .pain-area-type {
            width: 100%;
            min-height: 38px;
            border: 1px solid #dce7e5;
            border-radius: 14px;
            padding: 0 10px;
            color: #20343a;
            background: #ffffff;
            font-size: 13px;
            font-weight: 900;
            font-family: inherit;
            outline: none;
        }

        .pain-behavior-grid {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .pain-behavior-card {
            border: 1px solid #dbecea;
            border-radius: 18px;
            padding: 14px;
            background: #ffffff;
        }

        .pain-behavior-card label {
            margin-bottom: 9px;
        }

        .pain-behavior-card textarea {
            min-height: 108px;
            background: #fbfffe;
        }

        .pain-body-form-panel {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .pain-main-line {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .pain-note-card {
            grid-column: 1 / -1;
        }

        @media (max-width: 900px) {
            .pain-area-row {
                grid-template-columns: 1fr;
            }

            .pain-behavior-grid {
                grid-template-columns: 1fr;
            }
        }

    
        .content-grid.clinical-full-layout {
            display: block;
        }

        .patient-summary-strip {
            margin-bottom: 18px;
            border: 1px solid #dbecea;
            border-radius: 24px;
            background: rgba(255,255,255,.92);
            box-shadow: 0 18px 45px rgba(31,79,77,.08);
            padding: 18px;
        }

        .patient-summary-strip-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .patient-summary-strip-title {
            margin: 0;
            color: #1f4f4d;
            font-size: 22px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .patient-summary-strip-subtitle {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.7;
            font-weight: 750;
        }

        .patient-summary-strip-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
        }

        .patient-summary-strip .profile-box {
            margin: 0;
            min-height: 86px;
        }

        .clinical-main-wide {
            width: 100%;
        }

        .clinical-main-wide > .section-card {
            width: 100%;
        }

        .body-chart-layout {
            grid-template-columns: minmax(420px, .95fr) minmax(440px, 1.05fr);
            align-items: stretch;
        }

        .pain-area-row {
            grid-template-columns: minmax(160px, 1fr) 132px 170px 38px;
            gap: 12px;
            padding: 14px;
        }

        .pain-area-name {
            overflow-wrap: anywhere;
        }

        .pain-area-sub {
            max-width: 180px;
        }

        .pain-area-intensity,
        .pain-area-type {
            min-height: 42px;
        }

        .pain-body-form-panel {
            gap: 16px;
        }

        .pain-behavior-grid {
            gap: 16px;
        }

        .pain-note-card textarea {
            min-height: 132px;
        }

        @media (max-width: 1180px) {
            .patient-summary-strip-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .body-chart-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 680px) {
            .patient-summary-strip-grid {
                grid-template-columns: 1fr;
            }

            .patient-summary-strip-head {
                flex-direction: column;
            }

            .pain-area-row {
                grid-template-columns: 1fr;
            }

            .pain-area-sub {
                max-width: none;
            }
        }

    
        .body-chart-layout {
            display: block !important;
        }

        .body-map-panel {
            max-width: 760px;
            margin: 0 auto;
        }

        .anatomy-map-card {
            max-width: 100%;
        }

        .anatomy-stage {
            max-width: 520px;
            margin: 0 auto;
        }

        .pain-body-form-panel {
            margin-top: 18px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .pain-guidance-card {
            max-width: 100%;
        }

        .selected-area-card {
            margin-top: 16px;
        }

        .pain-area-list {
            gap: 12px;
        }

        .pain-area-row {
            grid-template-columns: minmax(220px, 1fr) 150px 190px 40px;
            gap: 14px;
            padding: 14px 16px;
        }

        .pain-area-sub {
            max-width: none;
        }

        .pain-area-intensity,
        .pain-area-type {
            min-height: 44px;
            border-radius: 16px;
        }

        .pain-advanced {
            grid-column: 1 / -1;
        }

        .pain-behavior-grid {
            grid-column: 1 / -1;
            grid-template-columns: 1fr 1fr;
        }

        .pain-note-card {
            grid-column: 1 / -1;
        }

        .pain-note-card textarea {
            min-height: 140px;
        }

        @media (max-width: 820px) {
            .pain-area-row {
                grid-template-columns: 1fr;
            }

            .pain-behavior-grid {
                grid-template-columns: 1fr;
            }
        }

    
        .body-chart-controls-grid {
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .body-chart-controls-grid .pain-area-hidden-field,
        .body-chart-controls-grid .pain-side-hidden-field,
        .body-chart-controls-grid .pain-type-hidden-field,
        .body-chart-controls-grid .pain-global-intensity-hidden {
            display: none !important;
        }

        .body-chart-controls-grid .pain-advanced,
        .body-chart-controls-grid .pain-behavior-grid,
        .body-chart-controls-grid .pain-note-card {
            grid-column: 1 / -1;
        }

        .body-chart-controls-grid .pain-note-card textarea {
            min-height: 132px;
        }

        @media (max-width: 820px) {
            .body-chart-controls-grid {
                grid-template-columns: 1fr;
            }
        }

    
        .goal-phase-card,
        .session-advanced-card {
            margin-top: 16px;
            border: 1px solid #dbecea;
            border-radius: 24px;
            background: linear-gradient(135deg, #fbfffe 0%, #ffffff 58%, #f7fffc 100%);
            padding: 18px;
            box-shadow: 0 16px 38px rgba(31,79,77,.06);
        }

        .goal-phase-head,
        .session-advanced-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .goal-phase-title,
        .session-advanced-title {
            margin: 0;
            color: #1f4f4d;
            font-size: 18px;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .goal-phase-subtitle,
        .session-advanced-subtitle {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
            font-weight: 750;
        }

        .goal-phase-pill {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            border-radius: 999px;
            padding: 0 12px;
            background: #eefcf8;
            border: 1px solid #c9ebe4;
            color: #1f4f4d;
            font-size: 11px;
            font-weight: 950;
        }

        .goal-phase-grid,
        .session-advanced-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .goal-phase-grid .field.full,
        .session-advanced-grid .field.full {
            grid-column: 1 / -1;
        }

        .goal-phase-select-row {
            display: grid;
            grid-template-columns: minmax(0, 240px) 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        @media (max-width: 900px) {
            .goal-phase-grid,
            .session-advanced-grid,
            .goal-phase-select-row {
                grid-template-columns: 1fr;
            }
        }

    
        /* 5D UI Polish */
        .goal-phase-card {
            grid-column: 1 / -1;
            width: 100%;
            margin: 18px 0;
            padding: 22px;
            border-radius: 26px;
        }

        .goal-phase-head {
            align-items: center;
            margin-bottom: 18px;
        }

        .goal-phase-select-row {
            grid-template-columns: minmax(240px, 320px);
            max-width: 340px;
            margin-bottom: 18px;
        }

        .goal-phase-select-row .field:nth-child(2) {
            display: none;
        }

        .goal-phase-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .goal-phase-grid textarea {
            min-height: 150px;
            resize: vertical;
            line-height: 1.65;
        }

        .session-advanced-card {
            grid-column: 1 / -1;
            width: 100%;
            margin: 18px 0 0;
            padding: 22px;
            border-radius: 26px;
        }

        .session-advanced-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            align-items: stretch;
        }

        .session-advanced-grid .field:nth-child(3),
        .session-advanced-grid .field:nth-child(4) {
            max-width: 360px;
        }

        .session-advanced-grid textarea {
            min-height: 135px;
            resize: vertical;
            line-height: 1.65;
        }

        .session-advanced-grid select {
            min-height: 48px;
        }

        @media (max-width: 980px) {
            .goal-phase-grid,
            .session-advanced-grid {
                grid-template-columns: 1fr;
            }

            .goal-phase-select-row {
                grid-template-columns: 1fr;
                max-width: none;
            }

            .session-advanced-grid .field:nth-child(3),
            .session-advanced-grid .field:nth-child(4) {
                max-width: none;
            }
        }

    
        .rom-progress-card {
            grid-column: 1 / -1;
            width: 100%;
            margin: 18px 0 0;
            padding: 22px;
            border-radius: 26px;
            border: 1px solid #dbecea;
            background: linear-gradient(135deg, #fbfffe 0%, #ffffff 58%, #f7fffc 100%);
            box-shadow: 0 16px 38px rgba(31,79,77,.06);
        }

        .rom-progress-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
            margin-bottom: 16px;
        }

        .rom-progress-title {
            margin: 0;
            color: #1f4f4d;
            font-size: 18px;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .rom-progress-subtitle {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
            font-weight: 750;
        }

        .rom-progress-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            align-items: end;
        }

        .rom-progress-grid input,
        .rom-progress-grid select {
            min-height: 48px;
        }

        @media (max-width: 980px) {
            .rom-progress-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .rom-progress-grid {
                grid-template-columns: 1fr;
            }
        }

    
        .dry-needling-card {
            margin-top: 14px;
            border: 1px solid #dbecea;
            border-radius: 22px;
            padding: 16px;
            background: linear-gradient(135deg, #ffffff, #f6fffd);
        }

        .dry-needling-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .dry-needling-title {
            margin: 0;
            color: #1f4f4d;
            font-size: 15px;
            font-weight: 950;
        }

        .dry-needling-subtitle {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
            font-weight: 750;
        }

        .dry-needling-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        @media (max-width: 760px) {
            .dry-needling-grid {
                grid-template-columns: 1fr;
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


        /* Batch 5A - Medical Record Advanced Tabs */
        .mr-tabs-shell {
            margin-top: 18px;
        }

        .mr-tab-nav {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding: 12px;
            margin: 0 0 18px;
            border: 1px solid #e4efec;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.045);
        }

        .mr-tab-button {
            border: 1px solid #dceae7;
            background: #ffffff;
            color: #486168;
            cursor: pointer;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 900;
            font-family: Arial, sans-serif;
            transition: .18s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .mr-tab-button:hover {
            transform: translateY(-1px);
            border-color: #b9dad5;
            color: #2f7c7a;
        }

        .mr-tab-button.active {
            background: linear-gradient(135deg, #2f8f89 0%, #226e70 100%);
            border-color: #226e70;
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(47, 124, 122, 0.18);
        }

        .mr-tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            border-radius: 999px;
            background: rgba(47, 124, 122, 0.10);
            color: inherit;
            font-size: 11px;
            font-weight: 900;
        }

        .mr-tab-button.active .mr-tab-count {
            background: rgba(255, 255, 255, 0.18);
        }

        .mr-tab-panel {
            display: none;
            animation: mrTabFade .18s ease;
        }

        .mr-tab-panel.active {
            display: block;
        }

        .mr-tab-panel-head {
            margin-bottom: 16px;
            padding: 18px 20px;
            border-radius: 22px;
            border: 1px solid #e3efec;
            background: linear-gradient(135deg, #f8fffd 0%, #ffffff 100%);
        }

        .mr-tab-panel-kicker {
            display: inline-flex;
            padding: 7px 11px;
            border-radius: 999px;
            background: #eef7f5;
            color: #2f7c7a;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .mr-tab-panel-title {
            margin: 0;
            font-size: 24px;
            line-height: 1.2;
            color: #22343a;
            font-weight: 900;
        }


        .goal-phase-helper {
            display: none;
            margin: 12px 0 14px;
            padding: 12px 14px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px dashed #d8e5e3;
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.6;
        }

        .goal-phase-field.is-hidden {
            display: none;
        }

        .goal-phase-helper.is-visible {
            display: block;
        }

        .mr-tab-panel-text {
            margin: 8px 0 0;
            font-size: 13px;
            line-height: 1.8;
            color: #64748b;
        }

        .mr-tab-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin: 18px 0 0;
            padding-top: 18px;
            border-top: 1px solid #eef3f2;
        }

        .mr-tab-nav-btn {
            border: 1px solid #d8ebe7;
            background: #ffffff;
            color: #2f7c7a;
            cursor: pointer;
            min-height: 42px;
            padding: 0 15px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 900;
            font-family: Arial, sans-serif;
        }

        .mr-tab-nav-btn.primary {
            background: #2f7c7a;
            color: #ffffff;
            border-color: #2f7c7a;
        }

        @keyframes mrTabFade {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 760px) {
            .mr-tab-nav {
                position: relative;
                top: auto;
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .mr-tab-button {
                width: 100%;
                justify-content: center;
                padding: 0 10px;
                font-size: 12px;
            }

            .mr-tab-panel-head {
                padding: 16px;
            }

            .mr-tab-panel-title {
                font-size: 20px;
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
        : collect([(object)[
            'history_type' => '',
            'history_note' => '',
            'history_date' => '',
        ]]);

    $comorbidities = $record && $record->comorbidities && $record->comorbidities->count()
        ? $record->comorbidities
        : collect([(object)[
            'name' => '',
            'is_checked' => false,
            'measurement_date' => '',
            'final_value' => '',
            'note' => '',
        ]]);

    $supportingData = $record && $record->supportingData && $record->supportingData->count()
        ? $record->supportingData
        : collect([(object)[
            'data_date' => '',
            'data_type' => '',
            'interpretation' => '',
        ]]);

    $homeExercises = $record && $record->homeExercises && $record->homeExercises->count()
        ? $record->homeExercises
        : collect([(object)[
            'exercise' => '',
            'dosage' => '',
            'note_caution' => '',
        ]]);

    $clinicalRequiredFields = [
        'complaint',
        'pain_scale',
        'subjective_examination',
        'objective_examination',
        'physiotherapy_diagnosis',
        'impairment',
        'patient_goal',
        'program_patient',
        'treatment_given',
        'response_to_treatment',
        'next_session_plan',
    ];

    $completedClinicalFields = collect($clinicalRequiredFields)->filter(function ($field) use ($record) {
        return $record && !blank($record->{$field});
    })->count();

    $clinicalCompletion = count($clinicalRequiredFields) > 0
        ? round(($completedClinicalFields / count($clinicalRequiredFields)) * 100)
        : 0;

    $painScale = $record && $record->pain_scale !== null ? (int) $record->pain_scale : null;
    $painWidth = $painScale !== null ? min(max($painScale, 0), 10) * 10 : 0;

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

    $summaryIssues = collect();

    if (!$record || blank($record->complaint)) {
        $summaryIssues->push('Complaint belum diisi');
    }

    if (!$record || blank($record->pain_scale)) {
        $summaryIssues->push('Pain scale belum diisi');
    }

    if (!$record || blank($record->physiotherapy_diagnosis)) {
        $summaryIssues->push('Diagnosa belum diisi');
    }

    if (!$record || blank($record->program_patient)) {
        $summaryIssues->push('Program patient belum diisi');
    }

    if (!$record || blank($record->next_session_plan)) {
        $summaryIssues->push('Next session plan belum diisi');
    }
    $painBodyAreaItems = collect(json_decode($record->pain_body_areas ?? '[]', true) ?: []);

    if ($painBodyAreaItems->isEmpty() && $record && !blank($record->pain_body_area)) {
        $painBodyAreaItems = collect([$record->pain_body_area]);
    }

    $painQualityTagItems = collect(json_decode($record->pain_quality_tags ?? '[]', true) ?: []);
    $selectedPainQualityTags = old('pain_quality_tags', $painQualityTagItems->all());

@endphp

<div class="page">
    <div class="container">
        <div class="topbar">
            <div class="brand">Khayra Therapist Dashboard</div>
            <div class="topbar-actions">
                <a href="/therapist/visits/{{ $visit->id }}/report" class="ghost-link">View Report</a>
                <a href="/therapist/visits/{{ $visit->id }}/report/print" target="_blank" class="ghost-link">Print Report</a>
                <a href="/therapist/dashboard" class="ghost-link">← Kembali ke Dashboard</a>
            </div>
        </div>

        <section class="hero">
            <div class="hero-main">
                <div class="hero-badge">Medical Record V2</div>
                <h1 class="hero-title">Clinical assessment untuk {{ $visit->patient->full_name ?? 'Patient' }}</h1>
                <p class="hero-text">
                    Isi rekam medis secara lebih lengkap dan terstruktur sesuai alur fisioterapi:
                    complaint, examination, diagnosis, planning, dan home exercise program.
                </p>

                <div class="hero-tags">
                    <span class="hero-tag">Visit #{{ $visit->id }}</span>
                    <span class="hero-tag">{{ $visit->visit_date ?: '-' }}</span>
                    <span class="hero-tag">{{ $visit->status ?: '-' }}</span>
                </div>
            </div>

            <div class="hero-side">
                <h2 class="side-title">Ringkasan Visit</h2>
                <p class="side-subtitle">Informasi singkat visit yang sedang Anda tangani.</p>

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
                        <div class="mini-label">Therapist</div>
                        <div class="mini-value">{{ $visit->therapistRelation->full_name ?? $visit->therapist ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="clinical-summary-grid">
            <div class="clinical-summary-card">
                <h2 class="clinical-summary-title">Clinical Summary & Completeness</h2>
                <p class="clinical-summary-subtitle">
                    Ringkasan otomatis untuk membantu therapist mengecek apakah dokumentasi klinis sudah cukup lengkap sebelum disimpan atau diprint.
                </p>

                <div class="summary-metrics">
                    <div class="summary-metric">
                        <div class="summary-label">Completeness</div>
                        <div class="summary-value">{{ $clinicalCompletion }}%</div>
                    </div>

                    <div class="summary-metric">
                        <div class="summary-label">Pain Scale</div>
                        <div class="summary-value">{{ $painScale !== null ? $painScale . '/10' : '-' }}</div>
                    </div>

                    <div class="summary-metric">
                        <div class="summary-label">Home Exercise</div>
                        <div class="summary-value">{{ $record && $record->homeExercises ? $record->homeExercises->count() : 0 }} item</div>
                    </div>
                </div>

                <div class="completion-wrap">
                    <div class="completion-head">
                        <span>Clinical documentation progress</span>
                        <span>{{ $completedClinicalFields }} / {{ count($clinicalRequiredFields) }} fields</span>
                    </div>
                    <div class="completion-bar">
                        <div class="completion-fill" style="width: {{ $clinicalCompletion }}%;"></div>
                    </div>
                </div>

                <div class="pain-meter">
                    <div class="completion-head">
                        <span>Pain visual scale</span>
                        <span>{{ $painScale !== null ? $painScale . '/10' : 'Belum diisi' }}</span>
                    </div>
                    <div class="pain-bar">
                        <div class="pain-indicator" style="width: {{ $painWidth }}%;"></div>
                    </div>
                    <div class="pain-scale-row">
                        <span>0</span>
                        <span>2</span>
                        <span>4</span>
                        <span>6</span>
                        <span>8</span>
                        <span>10</span>
                    </div>
                </div>

                <div class="clinical-action-row">
                    <a href="/therapist/visits/{{ $visit->id }}/report" class="blue-action">View Clinical Report</a>
                    <a href="/therapist/visits/{{ $visit->id }}/report/print" target="_blank" class="soft-action">Print / Save PDF</a>
                </div>
            </div>

            <div class="clinical-summary-card">
                <h2 class="clinical-summary-title">Clinical Checklist</h2>
                <p class="clinical-summary-subtitle">
                    Item yang perlu dilengkapi agar rekam medis lebih siap untuk report, follow-up, dan patient portal.
                </p>

                @if($summaryIssues->count())
                    <div class="issue-list">
                        @foreach($summaryIssues as $issue)
                            <div class="issue-item">{{ $issue }}</div>
                        @endforeach
                    </div>
                @else
                    <div class="issue-ok">
                        Rekam medis utama sudah terlihat lengkap. Therapist bisa lanjut review report atau print bila diperlukan.
                    </div>
                @endif
            </div>
        </section>

        <section class="pain-history-card">
            <div class="pain-history-header">
                <div>
                    <div class="pain-history-eyebrow">Pain Progress</div>
                    <h2 class="pain-history-title">Pain Tracking per Session</h2>
                    <p class="pain-history-subtitle">
                        Grafik pain scale 0-10 dari setiap sesi/visit pasien. Saat pasien treatment lagi dan pain scale diisi, grafik otomatis bertambah.
                    </p>
                </div>

                <div class="pain-history-trend">
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
                <div class="pain-history-chart">
                    @foreach($painTrackingRecords as $painItem)
                        @php
                            $painScore = is_null($painItem->pain_scale) ? 0 : min(max((int) $painItem->pain_scale, 0), 10);
                            $painPercent = $painScore * 10;
                        @endphp

                        <div class="pain-history-row">
                            <div class="pain-history-date">
                                <span>Session</span>
                                {{ $painItem->visit_date ?: '-' }}
                            </div>

                            <div class="pain-history-track">
                                <div class="pain-history-fill" style="width: {{ max($painPercent, 4) }}%;"></div>
                            </div>

                            <div class="pain-history-score">{{ $painScore }}/10</div>
                        </div>
                    @endforeach
                </div>

                <div class="pain-history-footer">
                    @php
                        $latestPain = $painTrackingRecords->last();
                    @endphp

                    <div class="pain-history-note">
                        <strong>Latest pain:</strong>
                        {{ optional($latestPain)->pain_scale ?? '-' }}/10
                        @if(optional($latestPain)->pain_type)
                            · {{ optional($latestPain)->pain_type }}
                        @endif
                    </div>

                    <div class="pain-history-note">
                        <strong>Total session recorded:</strong> {{ $painTrackingRecords->count() }}
                    </div>
                </div>
            @else
                <div class="pain-history-empty">
                    Belum ada pain tracking yang tersimpan. Lengkapi catatan nyeri pasien lalu simpan rekam medis.
                </div>
            @endif
        </section>

@if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Periksa kembali input Anda:</strong>
                <ul style="margin:8px 0 0 18px; padding:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="content-grid clinical-full-layout">
            <div class="patient-summary-strip">
                <div class="patient-summary-strip-head">
                    <div>
                        <h2 class="patient-summary-strip-title">Patient Summary</h2>
                        <p class="patient-summary-strip-subtitle">Informasi dasar patient dan visit aktif sebelum therapist mengisi clinical notes.</p>
                    </div>
                </div>

                <div class="patient-summary-strip-grid">
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

            <div class="clinical-main-wide">
                <section class="section-card">
                    <h2 class="section-title">Clinical Notes</h2>
                <p class="section-subtitle">
                    Lengkapi assessment dan intervensi secara lebih terstruktur untuk kebutuhan klinis dan report therapist.
                </p>

                <form method="POST" action="/therapist/visits/{{ $visit->id }}/medical-record" enctype="multipart/form-data">
                    @csrf

                    <div class="mr-tabs-shell">
                        <div class="mr-tab-nav" role="tablist" aria-label="Medical Record Categories">
                            <button type="button" class="mr-tab-button active" data-mr-tab="anamnesa">Anamnesa <span class="mr-tab-count">3</span></button>
                            <button type="button" class="mr-tab-button" data-mr-tab="examination">Examination <span class="mr-tab-count">3</span></button>
                            <button type="button" class="mr-tab-button" data-mr-tab="diagnosis">Diagnosis <span class="mr-tab-count">1</span></button>
                            <button type="button" class="mr-tab-button" data-mr-tab="program">Program <span class="mr-tab-count">2</span></button>
                            <button type="button" class="mr-tab-button" data-mr-tab="intervention">Intervention <span class="mr-tab-count">2</span></button>
                            <button type="button" class="mr-tab-button" data-mr-tab="history">History <span class="mr-tab-count">1</span></button>
                        </div>

                        <div class="mr-tab-panel active" data-mr-panel="anamnesa">
                            <div class="mr-tab-panel-head">
                                <div class="mr-tab-panel-kicker">Anamnesa</div>
                                <h3 class="mr-tab-panel-title">Chief complaint, pain profile, medical history, dan comorbidities.</h3>
                                <p class="mr-tab-panel-text">Isi keluhan utama, peta nyeri, riwayat medis, dan komorbid pasien.</p>
                            </div>

                    <div class="form-section">
                        <h3 class="form-section-title">1. Chief Complaint & Pain Profile</h3>
                        <p class="form-section-text">Keluhan utama, onset, karakter nyeri, dan keterbatasan fungsi awal.</p>

                        <div class="field">
                            <label>Complaint</label>
                            <textarea name="complaint" placeholder="Keluhan utama pasien">{{ old('complaint', $record->complaint ?? '') }}</textarea>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Onset</label>
                                <input type="text" name="onset" value="{{ old('onset', $record->onset ?? '') }}" placeholder="Contoh: 2 minggu yang lalu">
                            </div>

                            <input type="hidden" name="pain_type" value="{{ old('pain_type', $record->pain_type ?? '') }}">
                        </div>

                        <div class="grid-2">
                            <input type="hidden" name="pain_scale" value="{{ old('pain_scale', $record->pain_scale ?? '') }}">

                            <div class="field">
                                <label>Functional Limitation (Initial)</label>
                                <input type="text" name="functional_limitation_initial" value="{{ old('functional_limitation_initial', $record->functional_limitation_initial ?? '') }}" placeholder="Contoh: sulit duduk lama / berjalan">
                            </div>
                        </div>

                        <div class="field">
                            <label>Condition Felt</label>
                            <textarea name="condition_felt" placeholder="Kondisi yang dirasakan pasien">{{ old('condition_felt', $record->condition_felt ?? '') }}</textarea>
                        </div>
                                                                                                <div class="body-chart-card">
                            <div class="body-chart-head">
                                <div>
                                    <h4 class="body-chart-title">Pain Body Chart</h4>
                                    <p class="body-chart-subtitle">
                                        Klik area anatomi yang nyeri. Pilihan bisa lebih dari satu area.
                                    </p>
                                </div>

                                <div class="body-chart-badge">Premium Anatomy Map</div>
                            </div>

                            <div class="body-map-wrap">
                                <div class="anatomy-map-shell">
                                    <div class="anatomy-map-meta">
                                        <div class="anatomy-map-title">Interactive Anatomy Map</div>
                                        <div class="anatomy-map-pill">Klik Area Nyeri</div>
                                    </div>

                                    <div class="anatomy-side-tabs">
                                        <button type="button" class="anatomy-side-tab active" data-view="front">Front View</button>
                                        <button type="button" class="anatomy-side-tab" data-view="back">Back View</button>
                                    </div>

                                    <div class="anatomy-svg-wrap">
                                        <div class="anatomy-svg-panel active" data-panel="front">

                                                <svg class="anatomy-svg" viewBox="0 0 240 500" aria-label="Front anatomy map">
                                                    <path class="anatomy-base" d="M120 30 C95 30 82 49 84 70 C86 93 101 105 120 105 C139 105 154 93 156 70 C158 49 145 30 120 30Z"/>
                                                    <path class="anatomy-base" d="M104 103 L136 103 L142 132 L98 132Z"/>
                                                    <path class="anatomy-base" d="M75 134 C91 111 149 111 165 134 C179 154 174 214 160 260 C151 290 89 290 80 260 C66 214 61 154 75 134Z"/>
                                                    <path class="anatomy-base" d="M86 262 C101 283 139 283 154 262 L165 318 C151 336 89 336 75 318Z"/>
                                                    <path class="anatomy-base" d="M72 143 C49 154 39 195 31 245 C28 263 44 268 51 251 L74 173Z"/>
                                                    <path class="anatomy-base" d="M168 143 C191 154 201 195 209 245 C212 263 196 268 189 251 L166 173Z"/>
                                                    <path class="anatomy-base" d="M45 253 C35 285 33 321 39 352 C43 371 59 369 61 350 L61 276Z"/>
                                                    <path class="anatomy-base" d="M195 253 C205 285 207 321 201 352 C197 371 181 369 179 350 L179 276Z"/>
                                                    <path class="anatomy-base" d="M88 324 C76 362 75 414 82 461 C86 485 105 482 106 458 L113 330Z"/>
                                                    <path class="anatomy-base" d="M152 324 C164 362 165 414 158 461 C154 485 135 482 134 458 L127 330Z"/>
                                                    <path class="anatomy-detail" d="M120 132 L120 283"/>
                                                    <path class="anatomy-detail" d="M90 164 C108 174 132 174 150 164"/>
                                                    <path class="anatomy-detail" d="M93 212 C111 220 129 220 147 212"/>
                                                    <path class="anatomy-region" data-area="Head/Neck" d="M120 35 C99 35 90 50 91 69 C92 88 105 99 120 99 C135 99 148 88 149 69 C150 50 141 35 120 35Z"/>
                                                    <path class="anatomy-region" data-area="Head/Neck" d="M105 104 L135 104 L139 130 L101 130Z"/>
                                                    <path class="anatomy-region" data-area="Shoulder" d="M77 132 C89 118 151 118 163 132 L151 154 C137 143 103 143 89 154Z"/>
                                                    <path class="anatomy-region" data-area="Chest/Rib" d="M91 153 C109 143 131 143 149 153 C157 171 157 197 149 214 C130 207 110 207 91 214 C83 197 83 171 91 153Z"/>
                                                    <path class="anatomy-region" data-area="Abdomen/Core" d="M91 218 C110 227 130 227 149 218 C151 242 145 264 120 269 C95 264 89 242 91 218Z"/>
                                                    <path class="anatomy-region" data-area="Hip/Pelvis" d="M86 270 C101 289 139 289 154 270 L161 314 C143 328 97 328 79 314Z"/>
                                                    <path class="anatomy-region" data-area="Arm/Elbow" d="M73 150 C54 164 47 205 39 247 C49 253 57 251 62 239 L82 165Z"/>
                                                    <path class="anatomy-region" data-area="Arm/Elbow" d="M167 150 C186 164 193 205 201 247 C191 253 183 251 178 239 L158 165Z"/>
                                                    <path class="anatomy-region" data-area="Wrist/Hand" d="M42 258 C35 288 35 322 41 350 C48 356 55 354 56 345 L56 274Z"/>
                                                    <path class="anatomy-region" data-area="Wrist/Hand" d="M198 258 C205 288 205 322 199 350 C192 356 185 354 184 345 L184 274Z"/>
                                                    <path class="anatomy-region" data-area="Thigh/Knee" d="M87 329 C78 365 79 401 84 425 L105 425 L112 331Z"/>
                                                    <path class="anatomy-region" data-area="Thigh/Knee" d="M153 329 C162 365 161 401 156 425 L135 425 L128 331Z"/>
                                                    <path class="anatomy-region" data-area="Calf/Ankle" d="M85 429 L105 429 L103 462 C101 482 88 482 84 462Z"/>
                                                    <path class="anatomy-region" data-area="Calf/Ankle" d="M155 429 L135 429 L137 462 C139 482 152 482 156 462Z"/>
                                                    <path class="anatomy-region" data-area="Foot" d="M82 462 C89 475 103 475 112 466 C114 483 101 492 86 487 C78 484 76 474 82 462Z"/>
                                                    <path class="anatomy-region" data-area="Foot" d="M158 462 C151 475 137 475 128 466 C126 483 139 492 154 487 C162 484 164 474 158 462Z"/>
                                                    <text class="anatomy-label" x="120" y="74">Head</text>
                                                    <text class="anatomy-label" x="120" y="122">Neck</text>
                                                    <text class="anatomy-label" x="120" y="148">Shoulder</text>
                                                    <text class="anatomy-label" x="120" y="185">Chest</text>
                                                    <text class="anatomy-label" x="120" y="246">Core</text>
                                                    <text class="anatomy-label" x="120" y="305">Hip</text>
                                                    <text class="anatomy-label" x="56" y="215">Arm</text>
                                                    <text class="anatomy-label" x="184" y="215">Arm</text>
                                                    <text class="anatomy-label" x="95" y="393">Knee</text>
                                                    <text class="anatomy-label" x="145" y="393">Knee</text>
                                                </svg>

                                        </div>

                                        <div class="anatomy-svg-panel" data-panel="back">

                                                <svg class="anatomy-svg" viewBox="0 0 240 500" aria-label="Back anatomy map">
                                                    <path class="anatomy-base" d="M120 30 C95 30 82 49 84 70 C86 93 101 105 120 105 C139 105 154 93 156 70 C158 49 145 30 120 30Z"/>
                                                    <path class="anatomy-base" d="M104 103 L136 103 L142 132 L98 132Z"/>
                                                    <path class="anatomy-base" d="M75 134 C91 111 149 111 165 134 C179 154 174 214 160 260 C151 290 89 290 80 260 C66 214 61 154 75 134Z"/>
                                                    <path class="anatomy-base" d="M86 262 C101 283 139 283 154 262 L165 318 C151 336 89 336 75 318Z"/>
                                                    <path class="anatomy-base" d="M72 143 C49 154 39 195 31 245 C28 263 44 268 51 251 L74 173Z"/>
                                                    <path class="anatomy-base" d="M168 143 C191 154 201 195 209 245 C212 263 196 268 189 251 L166 173Z"/>
                                                    <path class="anatomy-base" d="M45 253 C35 285 33 321 39 352 C43 371 59 369 61 350 L61 276Z"/>
                                                    <path class="anatomy-base" d="M195 253 C205 285 207 321 201 352 C197 371 181 369 179 350 L179 276Z"/>
                                                    <path class="anatomy-base" d="M88 324 C76 362 75 414 82 461 C86 485 105 482 106 458 L113 330Z"/>
                                                    <path class="anatomy-base" d="M152 324 C164 362 165 414 158 461 C154 485 135 482 134 458 L127 330Z"/>
                                                    <path class="anatomy-detail" d="M120 104 L120 318"/>
                                                    <path class="anatomy-detail" d="M95 150 C108 141 132 141 145 150"/>
                                                    <path class="anatomy-detail" d="M91 212 C111 224 129 224 149 212"/>
                                                    <path class="anatomy-region" data-area="Head/Neck" d="M120 35 C99 35 90 50 91 69 C92 88 105 99 120 99 C135 99 148 88 149 69 C150 50 141 35 120 35Z"/>
                                                    <path class="anatomy-region" data-area="Head/Neck" d="M105 104 L135 104 L139 130 L101 130Z"/>
                                                    <path class="anatomy-region" data-area="Shoulder" d="M77 132 C89 118 151 118 163 132 L151 156 C137 145 103 145 89 156Z"/>
                                                    <path class="anatomy-region" data-area="Upper Back" d="M90 154 C110 142 130 142 150 154 C158 176 156 198 148 216 C129 207 111 207 92 216 C84 198 82 176 90 154Z"/>
                                                    <path class="anatomy-region" data-area="Thoracic/Mid Back" d="M92 218 C111 227 129 227 148 218 C151 239 145 260 120 267 C95 260 89 239 92 218Z"/>
                                                    <path class="anatomy-region" data-area="Lower Back" d="M90 268 C105 280 135 280 150 268 L157 306 C139 319 101 319 83 306Z"/>
                                                    <path class="anatomy-region" data-area="Hip/Pelvis" d="M82 307 C100 325 140 325 158 307 L162 320 C144 336 96 336 78 320Z"/>
                                                    <path class="anatomy-region" data-area="Arm/Elbow" d="M73 150 C54 164 47 205 39 247 C49 253 57 251 62 239 L82 165Z"/>
                                                    <path class="anatomy-region" data-area="Arm/Elbow" d="M167 150 C186 164 193 205 201 247 C191 253 183 251 178 239 L158 165Z"/>
                                                    <path class="anatomy-region" data-area="Wrist/Hand" d="M42 258 C35 288 35 322 41 350 C48 356 55 354 56 345 L56 274Z"/>
                                                    <path class="anatomy-region" data-area="Wrist/Hand" d="M198 258 C205 288 205 322 199 350 C192 356 185 354 184 345 L184 274Z"/>
                                                    <path class="anatomy-region" data-area="Thigh/Knee" d="M87 329 C78 365 79 401 84 425 L105 425 L112 331Z"/>
                                                    <path class="anatomy-region" data-area="Thigh/Knee" d="M153 329 C162 365 161 401 156 425 L135 425 L128 331Z"/>
                                                    <path class="anatomy-region" data-area="Calf/Ankle" d="M85 429 L105 429 L103 462 C101 482 88 482 84 462Z"/>
                                                    <path class="anatomy-region" data-area="Calf/Ankle" d="M155 429 L135 429 L137 462 C139 482 152 482 156 462Z"/>
                                                    <path class="anatomy-region" data-area="Foot" d="M82 462 C89 475 103 475 112 466 C114 483 101 492 86 487 C78 484 76 474 82 462Z"/>
                                                    <path class="anatomy-region" data-area="Foot" d="M158 462 C151 475 137 475 128 466 C126 483 139 492 154 487 C162 484 164 474 158 462Z"/>
                                                    <text class="anatomy-label" x="120" y="122">Neck</text>
                                                    <text class="anatomy-label" x="120" y="151">Shoulder</text>
                                                    <text class="anatomy-label" x="120" y="185">Upper</text>
                                                    <text class="anatomy-label" x="120" y="244">Mid</text>
                                                    <text class="anatomy-label" x="120" y="294">Lower</text>
                                                    <text class="anatomy-label" x="120" y="323">Hip</text>
                                                    <text class="anatomy-label" x="56" y="215">Arm</text>
                                                    <text class="anatomy-label" x="184" y="215">Arm</text>
                                                    <text class="anatomy-label" x="95" y="450">Calf</text>
                                                    <text class="anatomy-label" x="145" y="450">Calf</text>
                                                </svg>

                                        </div>
                                    </div>

                                    <div class="body-map-helper">
                                        Klik area pada anatomy map untuk menambah atau melepas lokasi nyeri. Gunakan Front/Back View sesuai lokasi keluhan.
                                    </div>

                                    <input type="hidden" name="pain_body_areas" id="painBodyAreas" value="{{ old('pain_body_areas', $painBodyAreaItems->values()->toJson()) }}">
                                </div>

                                <div class="pain-body-form-panel">
                                    <div class="pain-guidance-card">
                                        <strong>Cara isi:</strong> klik satu atau beberapa area pada anatomy map. Setelah itu isi intensity dan tipe nyeri pada setiap area yang muncul.
                                    </div>

                                    <div class="selected-area-card">
                                        <div class="selected-area-title">Area Nyeri Terpilih</div>
                                        <div class="pain-area-list" id="selectedPainAreasList"></div>
                                    </div>
                                    <div class="body-chart-grid body-chart-controls-grid">
                                        <div class="field pain-area-hidden-field">
                                            <label>Area Nyeri</label>
                                            <select name="pain_body_area" id="painBodyArea">
                                                <option value="">Pilih area</option>
                                                @foreach(['Head/Neck','Shoulder','Chest/Rib','Abdomen/Core','Arm/Elbow','Wrist/Hand','Upper Back','Thoracic/Mid Back','Lower Back','Hip/Pelvis','Thigh/Knee','Calf/Ankle','Foot','Multiple Area'] as $area)
                                                    <option value="{{ $area }}" {{ old('pain_body_area', $record->pain_body_area ?? '') === $area ? 'selected' : '' }}>{{ $area }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="field pain-side-hidden-field">
                                            <label>Sisi Tubuh</label>
                                            <select name="pain_body_side" id="painBodySide">
                                                <option value="">Pilih sisi</option>
                                                @foreach(['Kanan','Kiri','Bilateral','Tengah','Menyebar'] as $side)
                                                    <option value="{{ $side }}" {{ old('pain_body_side', $record->pain_body_side ?? '') === $side ? 'selected' : '' }}>{{ $side }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="field pain-type-hidden-field">
                                            <label>Tipe Nyeri</label>
                                            <select name="pain_body_type" id="painBodyGlobalType">
                                                <option value="">Pilih tipe</option>
                                                @foreach(['Tajam','Tumpul','Terbakar','Kesemutan','Kebas','Tertarik','Berdenyut','Menjalar','Kaku','Spasm','Weakness'] as $type)
                                                    <option value="{{ $type }}" {{ old('pain_body_type', $record->pain_body_type ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="field pain-global-intensity-hidden">
                                            <label>Intensity</label>
                                            <select name="pain_body_intensity" id="painBodyGlobalIntensity">
                                                <option value="">0-10</option>
                                                @for($i = 0; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ (string) old('pain_body_intensity', $record->pain_body_intensity ?? '') === (string) $i ? 'selected' : '' }}>{{ $i }}/10</option>
                                                @endfor
                                            </select>
                                        </div>



                                        <details class="pain-advanced">
                                            <summary>Opsional: checklist kualitas nyeri umum</summary>
                                            <div class="pain-advanced-inner">
                                                <div class="pain-quality-grid">
                                                @foreach(['Tajam','Tumpul','Terbakar','Kesemutan','Kebas','Tertarik','Berdenyut','Menjalar','Kaku','Spasm','Weakness'] as $quality)
                                                    <label class="pain-quality-check">
                                                        <input type="checkbox" name="pain_quality_tags[]" value="{{ $quality }}" {{ in_array($quality, $selectedPainQualityTags ?? []) ? 'checked' : '' }}>
                                                        <span>{{ $quality }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            </div>
                                        </details>

                                        <div class="pain-behavior-grid">
                                            <div class="pain-behavior-card">
                                                <label>Nyeri Memburuk Saat / Trigger</label>
                                                <textarea name="pain_aggravating_activity" placeholder="Contoh: duduk lama, menoleh kanan, berjalan jauh, naik tangga">{{ old('pain_aggravating_activity', $record->pain_aggravating_activity ?? '') }}</textarea>
                                            </div>

                                            <div class="pain-behavior-card">
                                                <label>Nyeri Membaik Saat / Relief</label>
                                                <textarea name="pain_easing_activity" placeholder="Contoh: istirahat, stretching, kompres hangat, posisi tidur tertentu">{{ old('pain_easing_activity', $record->pain_easing_activity ?? '') }}</textarea>
                                            </div>
                                        </div>

                                        <div class="field full pain-note-card">
                                            <label>Catatan Nyeri</label>
                                            <textarea name="pain_body_chart_note" placeholder="Contoh: Nyeri dominan area leher kanan sampai upper trapezius, meningkat saat duduk lama dan menoleh ke kanan.">{{ old('pain_body_chart_note', $record->pain_body_chart_note ?? '') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="body-region-preview">
                                        <strong>Ringkasan:</strong> area nyeri + intensitas per area + tipe nyeri per area + catatan klinis.
                                    </div>
                                </div>
                            </div>
                        </div>
</div>

                    <div class="form-section">
                        <h3 class="form-section-title">2. Medical History</h3>
                        <p class="form-section-text">Riwayat injury, surgery, fracture, stroke, malignancy, atau riwayat lain.</p>

                        <div id="history-wrapper">
                            @foreach($histories as $index => $history)
                                <div class="inline-card history-row">
                                    <div class="inline-head">
                                        <div class="inline-title">History Item</div>
                                        <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
                                    </div>

                                    <div class="grid-3">
                                        <div class="field">
                                            <label>History Type</label>
                                            <input type="text" name="history_type[]" value="{{ old('history_type.' . $index, $history->history_type ?? '') }}" placeholder="injury / surgery / fracture / other">
                                        </div>

                                        <div class="field">
                                            <label>History Date</label>
                                            <input type="date" name="history_date[]" value="{{ old('history_date.' . $index, !empty($history->history_date) ? (string) $history->history_date : '') }}">
                                        </div>

                                        <div class="field">
                                            <label>History Note</label>
                                            <input type="text" name="history_note[]" value="{{ old('history_note.' . $index, $history->history_note ?? '') }}" placeholder="Catatan singkat">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="tiny-btn" onclick="addHistoryRow()">+ Tambah History</button>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">3. Comorbidities</h3>
                        <p class="form-section-text">Catat komorbid, status cek, tanggal pengukuran, dan nilai akhir bila ada.</p>

                        <div id="comorbidity-wrapper">
                            @foreach($comorbidities as $index => $comorbidity)
                                <div class="inline-card comorbidity-row">
                                    <div class="inline-head">
                                        <div class="inline-title">Comorbidity Item</div>
                                        <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
                                    </div>

                                    <div class="grid-3">
                                        <div class="field">
                                            <label>Name</label>
                                            <input type="text" name="comorbidity_name[]" value="{{ old('comorbidity_name.' . $index, $comorbidity->name ?? '') }}" placeholder="Contoh: diabetes / hipertensi">
                                        </div>

                                        <div class="field">
                                            <label>Measurement Date</label>
                                            <input type="date" name="comorbidity_measurement_date[]" value="{{ old('comorbidity_measurement_date.' . $index, !empty($comorbidity->measurement_date) ? (string) $comorbidity->measurement_date : '') }}">
                                        </div>

                                        <div class="field">
                                            <label>Final Value</label>
                                            <input type="text" name="comorbidity_final_value[]" value="{{ old('comorbidity_final_value.' . $index, $comorbidity->final_value ?? '') }}" placeholder="Nilai akhir / hasil ukur">
                                        </div>
                                    </div>

                                    <div class="grid-2">
                                        <div class="field">
                                            <label>Note</label>
                                            <input type="text" name="comorbidity_note[]" value="{{ old('comorbidity_note.' . $index, $comorbidity->note ?? '') }}" placeholder="Catatan tambahan">
                                        </div>

                                        <div class="field">
                                            <label>Checked</label>
                                            <div class="checkbox-row">
                                                <input type="checkbox" name="comorbidity_checked[{{ $index }}]" {{ old('comorbidity_checked.' . $index, $comorbidity->is_checked ?? false) ? 'checked' : '' }}>
                                                <span>Tandai bila komorbid ini aktif / relevan</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="tiny-btn" onclick="addComorbidityRow()">+ Tambah Comorbidity</button>
                    </div>

                            <div class="mr-tab-actions">
                                <span></span>
                                <button type="button" class="mr-tab-nav-btn primary" data-mr-next="examination">Next: Examination →</button>
                            </div>
                        </div>

                        <div class="mr-tab-panel" data-mr-panel="examination">
                            <div class="mr-tab-panel-head">
                                <div class="mr-tab-panel-kicker">Examination</div>
                                <h3 class="mr-tab-panel-title">Vital sign, supporting data, dan subjective-objective examination.</h3>
                                <p class="mr-tab-panel-text">Lengkapi pemeriksaan objektif dan data penunjang seperti X-ray, MRI, lab, atau evaluasi lain.</p>
                            </div>

                    <div class="form-section">
                        <h3 class="form-section-title">4. Vital Signs</h3>
                        <p class="form-section-text">Masukkan vital sign dasar yang dibutuhkan pada assessment.</p>

                        <div class="grid-3">
                            <div class="field">
                                <label>Blood Pressure</label>
                                <input type="text" name="blood_pressure" value="{{ old('blood_pressure', $record->blood_pressure ?? '') }}" placeholder="Contoh: 120/80">
                            </div>

                            <div class="field">
                                <label>Temperature</label>
                                <input type="text" name="temperature" value="{{ old('temperature', $record->temperature ?? '') }}" placeholder="Contoh: 36.5 C">
                            </div>

                            <div class="field">
                                <label>Respiration Rate</label>
                                <input type="text" name="respiration_rate" value="{{ old('respiration_rate', $record->respiration_rate ?? '') }}" placeholder="Contoh: 18 x/min">
                            </div>

                            <div class="field">
                                <label>Heart Rate</label>
                                <input type="text" name="heart_rate" value="{{ old('heart_rate', $record->heart_rate ?? '') }}" placeholder="Contoh: 72 bpm">
                            </div>

                            <div class="field">
                                <label>Weight</label>
                                <input type="text" name="weight" value="{{ old('weight', $record->weight ?? '') }}" placeholder="Contoh: 60 kg">
                            </div>

                            <div class="field">
                                <label>Height</label>
                                <input type="text" name="height" value="{{ old('height', $record->height ?? '') }}" placeholder="Contoh: 165 cm">
                            </div>
                        </div>

                        <div class="field">
                            <label>BMI</label>
                            <input type="text" name="bmi" value="{{ old('bmi', $record->bmi ?? '') }}" placeholder="Contoh: 22">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">5. Supporting Data Result</h3>
                        <p class="form-section-text">Tambahkan data penunjang seperti X-ray, MRI, lab, atau data evaluasi lain.</p>

                        <div id="supporting-data-wrapper">
                            @foreach(($supportingData ?? collect()) as $index => $item)
                                @php
                                    $supportingId = data_get($item, 'id', '');
                                    $supportingDateRaw = data_get($item, 'data_date');
                                    $supportingDate = '';

                                    if (!empty($supportingDateRaw)) {
                                        try {
                                            $supportingDate = \Carbon\Carbon::parse($supportingDateRaw)->format('Y-m-d');
                                        } catch (\Throwable $e) {
                                            $supportingDate = is_string($supportingDateRaw) ? substr($supportingDateRaw, 0, 10) : '';
                                        }
                                    }

                                    $supportingType = data_get($item, 'data_type', '');
                                    $supportingInterpretation = data_get($item, 'interpretation', '');
                                    $supportingFilePath = data_get($item, 'file_path', '');
                                    $supportingFileName = data_get($item, 'file_name', 'Lihat file');
                                @endphp

                                <div class="inline-card supporting-row">
                                    <input type="hidden" name="supporting_data_id[]" value="{{ old('supporting_data_id.' . $index, $supportingId) }}">
                                    <div class="inline-head">
                                        <div class="inline-title">Supporting Data Item</div>
                                        <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
                                    </div>

                                    <div class="grid-3">
                                        <div class="field">
                                            <label>Date</label>
                                            <input type="date" name="supporting_data_date[]" value="{{ old('supporting_data_date.' . $index, $supportingDate) }}">
                                        </div>

                                        <div class="field">
                                            <label>Type of Data</label>
                                            <input type="text" name="supporting_data_type[]" value="{{ old('supporting_data_type.' . $index, $supportingType) }}" placeholder="Contoh: X-ray / MRI / Lab">
                                        </div>

                                        <div class="field">
                                            <label>Interpretation</label>
                                            <input type="text" name="supporting_data_interpretation[]" value="{{ old('supporting_data_interpretation.' . $index, $supportingInterpretation) }}" placeholder="Interpretasi singkat">
                                        </div>

                                        <div class="field">
                                            <label>Upload File</label>
                                            <div class="file-upload-box">
                                                <input type="file" name="supporting_data_file[]" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx">
                                                @if(!empty($supportingFilePath))
                                                    <a class="current-file-pill" href="{{ asset('storage/' . $supportingFilePath) }}" target="_blank">
                                                        📎 {{ $supportingFileName ?: 'Lihat file' }}
                                                    </a>
                                                    <div class="file-upload-hint">Upload file baru hanya jika ingin mengganti attachment.</div>
                                                @else
                                                    <div class="file-upload-hint">Opsional: upload foto, PDF, atau dokumen hasil pemeriksaan.</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="tiny-btn" onclick="addSupportingDataRow()">+ Tambah Supporting Data</button>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">6. Objective Examination</h3>
                        <p class="form-section-text">Isi objective findings dan parameter klinis yang dibutuhkan pada assessment.</p>

                        <div style="display:none;">
                            <textarea name="subjective_examination">{{ old('subjective_examination', $record->subjective_examination ?? '') }}</textarea>
                        </div>

                        <div class="field">
                            <label>Objective Examination</label>
                            <textarea name="objective_examination" placeholder="Temuan objektif, ROM, palpasi, posture, dan temuan fisik lainnya">{{ old('objective_examination', $record->objective_examination ?? '') }}</textarea>
                        </div>

                        <div class="grid-3">
                            <div class="field">
                                <label>Severity Level</label>
                                <select name="severity_level">
                                    <option value="">Pilih severity</option>
                                    <option value="low" {{ old('severity_level', $record->severity_level ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('severity_level', $record->severity_level ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('severity_level', $record->severity_level ?? '') === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Irritability Level</label>
                                <select name="irritability_level">
                                    <option value="">Pilih irritability</option>
                                    <option value="low" {{ old('irritability_level', $record->irritability_level ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('irritability_level', $record->irritability_level ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('irritability_level', $record->irritability_level ?? '') === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Nature Type</label>
                                <select name="nature_type">
                                    <option value="">Pilih nature</option>
                                    <option value="mechanical" {{ old('nature_type', $record->nature_type ?? '') === 'mechanical' ? 'selected' : '' }}>Mechanical</option>
                                    <option value="non_mechanical" {{ old('nature_type', $record->nature_type ?? '') === 'non_mechanical' ? 'selected' : '' }}>Non Mechanical</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Easing Factors</label>
                                <textarea name="easing_factors" placeholder="Apa yang mengurangi keluhan">{{ old('easing_factors', $record->easing_factors ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Aggravating Factors</label>
                                <textarea name="aggravating_factors" placeholder="Apa yang memperberat keluhan">{{ old('aggravating_factors', $record->aggravating_factors ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="field">
                            <label>Special Test Notes</label>
                            <textarea name="special_test_notes" placeholder="Catatan special test / additional examination">{{ old('special_test_notes', $record->special_test_notes ?? '') }}</textarea>
                        </div>

                        <div class="rom-progress-card">
                            <div class="rom-progress-head">
                                <div>
                                    <h4 class="rom-progress-title">ROM / Functional Evaluation</h4>
                                    <p class="rom-progress-subtitle">Isi evaluasi ROM kanan/kiri dan fungsi agar progress pasien mudah dipantau.</p>
                                </div>
                                <div class="goal-phase-pill">ROM Chart</div>
                            </div>

                            <div class="rom-progress-grid">
                                <div class="field">
                                    <label>ROM Kanan</label>
                                    <input type="text" name="rom_cervical_rotation" value="{{ old('rom_cervical_rotation', $record->rom_cervical_rotation ?? '') }}" placeholder="Contoh: kanan 60° / ROM sisi kanan">
                                </div>

                                <div class="field">
                                    <label>ROM Kiri</label>
                                    <input type="text" name="rom_shoulder_elevation" value="{{ old('rom_shoulder_elevation', $record->rom_shoulder_elevation ?? '') }}" placeholder="Contoh: kiri 70° / ROM sisi kiri">
                                </div>

                                <div class="field">
                                    <label>Functional Score (0-100)</label>
                                    <select name="functional_score">
                                        <option value="">Pilih score</option>
                                        @for($i = 0; $i <= 100; $i += 10)
                                            <option value="{{ $i }}" {{ (string) old('functional_score', $record->functional_score ?? '') === (string) $i ? 'selected' : '' }}>{{ $i }}%</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="field">
                                    <label>Activity Tolerance</label>
                                    <input type="text" name="activity_tolerance" value="{{ old('activity_tolerance', $record->activity_tolerance ?? '') }}" placeholder="Contoh: duduk 60 menit">
                                </div>
                            </div>
                        </div>
                    </div>

                            <div class="mr-tab-actions">
                                <button type="button" class="mr-tab-nav-btn" data-mr-next="anamnesa">← Back: Anamnesa</button>
                                <button type="button" class="mr-tab-nav-btn primary" data-mr-next="diagnosis">Next: Diagnosis →</button>
                            </div>
                        </div>

                        <div class="mr-tab-panel" data-mr-panel="diagnosis">
                            <div class="mr-tab-panel-head">
                                <div class="mr-tab-panel-kicker">Diagnosis</div>
                                <h3 class="mr-tab-panel-title">Diagnosis, impairment, ICF, dan clinical decision making.</h3>
                                <p class="mr-tab-panel-text">Masukkan diagnosis fisioterapi, ICD/ICF, impairment, functional limitation, dan clinical reasoning.</p>
                            </div>

                    <div class="form-section">
                        <h3 class="form-section-title">7. Diagnosa & Clinical Decision</h3>
                        <p class="form-section-text">Tulis diagnosa, impairment, goal, referral, dan rencana program therapy.</p>

                        <div class="field">
                            <label>Diagnosa</label>
                            <textarea name="physiotherapy_diagnosis" placeholder="Diagnosa">{{ old('physiotherapy_diagnosis', $record->physiotherapy_diagnosis ?? '') }}</textarea>

                            <div class="form-grid two">
                                <div class="field">
                                    <label>ICD Code</label>
                                    <input type="text" name="icd_code" value="{{ old('icd_code', $record->icd_code ?? '') }}" placeholder="Contoh: M54.5">
                                    <div class="hint">Isi kode ICD bila sudah diketahui. Referensi bisa dari ICD-10 WHO.</div>
                                </div>

                                <div class="field">
                                    <label>ICD Diagnosis</label>
                                    <input type="text" name="icd_diagnosis" value="{{ old('icd_diagnosis', $record->icd_diagnosis ?? '') }}" placeholder="Contoh: Low back pain">
                                    <div class="hint">Nama diagnosis ICD atau diagnosis medis rujukan.</div>
                                </div>
                            </div>
                        </div>

                        <div style="display:none;">
                            <textarea name="impairment">{{ old('impairment', $record->impairment ?? '') }}</textarea>
                            <textarea name="functional_limitation_clinical">{{ old('functional_limitation_clinical', $record->functional_limitation_clinical ?? '') }}</textarea>
                        </div>

                        <div class="form-subsection">
                            <h4 class="mini-section-title">ICF Structure</h4>
                            <p class="form-section-text">Susun assessment sesuai kerangka ICF: Body Function, Body Structure, Activities & Participation, Personal Factors, dan Environmental Factors.</p>

                            <div class="form-grid two">
                                <div class="field">
                                    <label>Body Function</label>
                                    <textarea name="icf_body_function" placeholder="Contoh: nyeri, ROM terbatas, muscle weakness, balance deficit">{{ old('icf_body_function', $record->icf_body_function ?? '') }}</textarea>
                                </div>

                                <div class="field">
                                    <label>Body Structure</label>
                                    <textarea name="icf_body_structure" placeholder="Contoh: lumbar spine, knee joint, soft tissue, nerve structure">{{ old('icf_body_structure', $record->icf_body_structure ?? '') }}</textarea>
                                </div>

                                <div class="field">
                                    <label>Activities & Participation</label>
                                    <textarea name="icf_activities_participation" placeholder="Contoh: kesulitan berjalan, duduk lama, naik tangga, bekerja, olahraga">{{ old('icf_activities_participation', $record->icf_activities_participation ?? '') }}</textarea>
                                </div>

                                <div class="field">
                                    <label>Personal Factors</label>
                                    <textarea name="icf_personal_factors" placeholder="Contoh: usia, kebiasaan aktivitas, motivasi, pekerjaan, lifestyle">{{ old('icf_personal_factors', $record->icf_personal_factors ?? '') }}</textarea>
                                </div>

                                <div class="field full">
                                    <label>Environmental Factors</label>
                                    <textarea name="icf_environmental_factors" placeholder="Contoh: support keluarga, ergonomi kerja, alat bantu, lingkungan rumah">{{ old('icf_environmental_factors', $record->icf_environmental_factors ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        

                        <div class="grid-2">
                            <div class="field">
                                <label>Patient Goal</label>
                                <textarea name="patient_goal" placeholder="Target patient / goal terapi">{{ old('patient_goal', $record->patient_goal ?? '') }}</textarea>
                            </div>




                            <div class="field">
                                <label>Referral</label>
                                <textarea name="referral" placeholder="Rujukan bila ada">{{ old('referral', $record->referral ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="field">
                            <label>Program Patient</label>
                            <textarea name="program_patient" placeholder="Program terapi yang direncanakan">{{ old('program_patient', $record->program_patient ?? '') }}</textarea>
                        </div>

                        <div class="grid-3">
                            <div class="field">
                                <label>Date of Control</label>
                                <input type="date" name="date_of_control" value="{{ old('date_of_control', !empty($record->date_of_control) ? $record->date_of_control->format('Y-m-d') : '') }}">
                            </div>

                            <div class="field">
                                <label>Total Session</label>
                                <input type="number" min="0" name="total_session" value="{{ old('total_session', $record->total_session ?? '') }}" placeholder="Contoh: 6">
                            </div>

                            <div class="field">
                                <label>Frequency per Week</label>
                                <input type="text" name="frequency_per_week" value="{{ old('frequency_per_week', $record->frequency_per_week ?? '') }}" placeholder="Contoh: 2x/minggu">
                            </div>
                        </div>

                        <div class="field">
                            <label>Control Plan</label>
                            <textarea name="control_plan" placeholder="Rencana kontrol / follow up">{{ old('control_plan', $record->control_plan ?? '') }}</textarea>
                        </div>
                    </div>

                            <div class="mr-tab-actions">
                                <button type="button" class="mr-tab-nav-btn" data-mr-next="examination">← Back: Examination</button>
                                <button type="button" class="mr-tab-nav-btn primary" data-mr-next="program">Next: Program →</button>
                            </div>
                        </div>

                        <div class="mr-tab-panel" data-mr-panel="program">
                            <div class="mr-tab-panel-head">
                                <div class="mr-tab-panel-kicker">Program</div>
                                <h3 class="mr-tab-panel-title">Treatment goal phase, program patient, control plan, dan home exercise.</h3>
                                <p class="mr-tab-panel-text">Susun fase target terapi, program pasien, jadwal kontrol, edukasi, dan latihan rumah.</p>
                            </div>

                        <div class="goal-phase-card">
                            <div class="goal-phase-head">
                                <div>
                                    <h4 class="goal-phase-title">Treatment Goal Phase</h4>
                                    <p class="goal-phase-subtitle">Susun target terapi per fase agar progress pasien mudah dipantau di report.</p>
                                </div>
                                <div class="goal-phase-pill">Phase Plan</div>
                            </div>

                            <div class="goal-phase-select-row">
                                <div class="field">
                                    <label>Current Phase</label>
                                    <select name="goal_phase" id="goalPhaseSelect">
                                        <option value="">Pilih fase</option>
                                        @foreach(['Phase 1 - Pain Control','Phase 2 - Mobility / Strength','Phase 3 - Functional Return'] as $phase)
                                            <option value="{{ $phase }}" {{ old('goal_phase', $record->goal_phase ?? '') === $phase ? 'selected' : '' }}>{{ $phase }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="field">
                                    <label>Phase Summary</label>
                                    <input type="text" value="{{ old('goal_phase', $record->goal_phase ?? '') ?: 'Belum memilih fase aktif' }}" readonly>
                                </div>
                            </div>

                            <div class="goal-phase-helper" id="goalPhaseHelper">Pilih current phase terlebih dahulu. Kolom goal akan mengikuti fase yang dipilih.</div>

                            <div class="goal-phase-grid">
                                <div class="field goal-phase-field" data-goal-phase-field="Phase 1 - Pain Control">
                                    <label>Phase 1 Goal</label>
                                    <textarea name="phase_1_goal" placeholder="Contoh: nyeri turun, tidur lebih nyaman, edukasi posisi aman">{{ old('phase_1_goal', $record->phase_1_goal ?? '') }}</textarea>
                                </div>

                                <div class="field goal-phase-field" data-goal-phase-field="Phase 2 - Mobility / Strength">
                                    <label>Phase 2 Goal</label>
                                    <textarea name="phase_2_goal" placeholder="Contoh: ROM membaik, strength meningkat, toleransi aktivitas naik">{{ old('phase_2_goal', $record->phase_2_goal ?? '') }}</textarea>
                                </div>

                                <div class="field goal-phase-field" data-goal-phase-field="Phase 3 - Functional Return">
                                    <label>Phase 3 Goal</label>
                                    <textarea name="phase_3_goal" placeholder="Contoh: kembali kerja/olahraga, mandiri HEP, mencegah flare-up">{{ old('phase_3_goal', $record->phase_3_goal ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                    <div class="form-section">
                        <h3 class="form-section-title">8. Health Management</h3>
                        <p class="form-section-text">Edukasi terkait nutrisi, lifestyle, dan management flare-up.</p>

                        <div class="grid-3">
                            <div class="field">
                                <label>Diet / Nutrition</label>
                                <textarea name="diet_nutrition" placeholder="Catatan diet / nutrisi">{{ old('diet_nutrition', $record->diet_nutrition ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Lifestyle</label>
                                <textarea name="lifestyle" placeholder="Catatan lifestyle">{{ old('lifestyle', $record->lifestyle ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Flare-up Management</label>
                                <textarea name="flare_up_management" placeholder="Cara mengelola flare-up">{{ old('flare_up_management', $record->flare_up_management ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">9. Home Exercise Program</h3>
                        <p class="form-section-text">Tambahkan latihan rumah, dosis, dan catatan/caution.</p>

                        <div id="home-exercise-wrapper">
                            @foreach($homeExercises as $index => $exercise)
                                <div class="inline-card home-exercise-row">
                                    <div class="inline-head">
                                        <div class="inline-title">Home Exercise Item</div>
                                        <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
                                    </div>

                                    <div class="grid-3">
                                        <div class="field">
                                            <label>Pilih dari Exercise Library</label>
                                            <select onchange="applyHomeExerciseTemplate(this)">
                                                <option value="">Manual / pilih template</option>
                                                @foreach(($homeExerciseTemplates ?? collect()) as $template)
                                                    <option value="{{ $template->id }}">
                                                        {{ $template->name }}{{ $template->category ? ' · ' . $template->category : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="field">
                                            <label>Exercise</label>
                                            <input type="text" name="home_exercise_name[]" value="{{ old('home_exercise_name.' . $index, $exercise->exercise ?? '') }}" placeholder="Nama latihan">
                                        </div>

                                        <div class="field">
                                            <label>Dosage</label>
                                            <input type="text" name="home_exercise_dosage[]" value="{{ old('home_exercise_dosage.' . $index, $exercise->dosage ?? '') }}" placeholder="Contoh: 10 reps x 3 set">
                                        </div>

                                        <div class="field" style="grid-column:1/-1;">
                                            <label>Note / Caution</label>
                                            <input type="text" name="home_exercise_note[]" value="{{ old('home_exercise_note.' . $index, $exercise->note_caution ?? '') }}" placeholder="Catatan kehati-hatian">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="tiny-btn" onclick="addHomeExerciseRow()">+ Tambah Home Exercise</button>
                    </div>

                            <div class="mr-tab-actions">
                                <button type="button" class="mr-tab-nav-btn" data-mr-next="diagnosis">← Back: Diagnosis</button>
                                <button type="button" class="mr-tab-nav-btn primary" data-mr-next="intervention">Next: Intervention →</button>
                            </div>
                        </div>

                        <div class="mr-tab-panel" data-mr-panel="intervention">
                            <div class="mr-tab-panel-head">
                                <div class="mr-tab-panel-kicker">Intervention</div>
                                <h3 class="mr-tab-panel-title">Treatment given, response, recommendation, dan next session plan.</h3>
                                <p class="mr-tab-panel-text">Catat tindakan sesi ini, respons pasien, penggunaan inventory, rekomendasi, dan rencana sesi berikutnya.</p>
                            </div>

                    <div class="form-section">
                        <h3 class="form-section-title">10. Session Progress</h3>
                        <p class="form-section-text">Catatan intervensi sesi ini dan rencana sesi selanjutnya.</p>

                        <div class="field">
                            <label>Assessment (Legacy / Summary)</label>
                            <textarea name="assessment" placeholder="Ringkasan assessment singkat">{{ old('assessment', $record->assessment ?? '') }}</textarea>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Treatment (Legacy)</label>
                                <textarea name="treatment" placeholder="Treatment singkat versi lama">{{ old('treatment', $record->treatment ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Treatment Given</label>
                                <textarea name="treatment_given" placeholder="Intervensi yang diberikan di sesi ini">{{ old('treatment_given', $record->treatment_given ?? '') }}</textarea>
                            </div>

                            <div class="dry-needling-card">
                                <div class="dry-needling-head">
                                    <div>
                                        <h4 class="dry-needling-title">Dry Needling Inventory Usage</h4>
                                        <p class="dry-needling-subtitle">Catat penggunaan jarum dry needling agar stok inventory otomatis berkurang.</p>
                                    </div>
                                </div>

                                <div class="checkbox-row" style="margin-bottom:14px;">
                                    <input type="checkbox" name="dry_needling_done" value="1" {{ old('dry_needling_done', $record->dry_needling_done ?? false) ? 'checked' : '' }}>
                                    <span>Dry needling dilakukan pada sesi ini</span>
                                </div>

                                <div class="dry-needling-grid">
                                    <div class="field">
                                        <label>Inventory Item</label>
                                        <select name="dry_needling_inventory_item_id">
                                            <option value="">Pilih item jarum</option>
                                            @foreach($inventoryItems ?? collect() as $item)
                                                <option value="{{ $item->id }}" {{ (string) old('dry_needling_inventory_item_id', $record->dry_needling_inventory_item_id ?? '') === (string) $item->id ? 'selected' : '' }}>
                                                    {{ $item->name }} · Stock: {{ $item->stock }} {{ $item->unit }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="field">
                                        <label>Jumlah Jarum Dipakai</label>
                                        <input type="number" min="0" name="dry_needling_quantity" value="{{ old('dry_needling_quantity', $record->dry_needling_quantity ?? '') }}" placeholder="Contoh: 4">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Progress Note</label>
                                <textarea name="progress_note" placeholder="Progress pasien">{{ old('progress_note', $record->progress_note ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Response to Treatment</label>
                                <textarea name="response_to_treatment" placeholder="Respon pasien terhadap terapi">{{ old('response_to_treatment', $record->response_to_treatment ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Recommendation</label>
                                <textarea name="recommendation" placeholder="Rekomendasi umum">{{ old('recommendation', $record->recommendation ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Next Session Plan</label>
                                <textarea name="next_session_plan" placeholder="Rencana visit berikutnya">{{ old('next_session_plan', $record->next_session_plan ?? '') }}</textarea>
                            </div>




                        <div style="display:none;">
                            <textarea name="session_focus">{{ old('session_focus', $record->session_focus ?? '') }}</textarea>
                            <textarea name="session_progress_note">{{ old('session_progress_note', $record->session_progress_note ?? '') }}</textarea>
                            <input type="hidden" name="session_homework_status" value="{{ old('session_homework_status', $record->session_homework_status ?? '') }}">
                            <input type="hidden" name="session_pain_after" value="{{ old('session_pain_after', $record->session_pain_after ?? '') }}">
                        </div>

                        </div>
                    </div>

                            <div class="mr-tab-actions">
                                <button type="button" class="mr-tab-nav-btn" data-mr-next="program">← Back: Program</button>
                                <button type="button" class="mr-tab-nav-btn primary" data-mr-next="history">Next: History →</button>
                            </div>
                        </div>

                        <div class="mr-tab-panel" data-mr-panel="history">
                            <div class="mr-tab-panel-head">
                                <div class="mr-tab-panel-kicker">History</div>
                                <h3 class="mr-tab-panel-title">Medical record update history dan audit timeline.</h3>
                                <p class="mr-tab-panel-text">Lihat snapshot perubahan rekam medis untuk kebutuhan audit internal dan timeline pasien.</p>
                            </div>

                    <div class="form-section">
                        <h3 class="form-section-title">11. Medical Record Update History</h3>
                        <p class="form-section-text">Setiap kali Medical Record disimpan, sistem mencatat snapshot update untuk audit internal dan timeline pasien.</p>

                        @php
                            $medicalRecordUpdateLogs = collect();
                            if ($record && class_exists(\App\Models\MedicalRecordUpdateLog::class)) {
                                $medicalRecordUpdateLogs = \App\Models\MedicalRecordUpdateLog::where('medical_record_id', $record->id)
                                    ->latest('snapshot_date')
                                    ->take(8)
                                    ->get();
                            }
                        @endphp

                        @if($medicalRecordUpdateLogs->count())
                            <div style="display:grid;gap:12px;">
                                @foreach($medicalRecordUpdateLogs as $log)
                                    <div class="inline-card">
                                        <div class="inline-head">
                                            <div class="inline-title">
                                                {{ $log->snapshot_date ? $log->snapshot_date->format('Y-m-d H:i') : '-' }}
                                                · {{ $log->updated_by_name ?: 'Therapist' }}
                                            </div>
                                            <span class="summary-pill">Pain {{ is_null($log->pain_scale) ? '-' : $log->pain_scale . '/10' }}</span>
                                        </div>
                                        <div style="font-size:13px;line-height:1.8;color:#475569;">
                                            <strong>Response:</strong> {{ $log->response_to_treatment ?: '-' }}<br>
                                            <strong>Next Plan:</strong> {{ $log->next_session_plan ?: '-' }}<br>
                                            <strong>Next Control:</strong> {{ $log->date_of_control ? $log->date_of_control->format('Y-m-d') : '-' }} ·
                                            <strong>Frequency:</strong> {{ $log->frequency_per_week ?: '-' }} ·
                                            <strong>Total Session:</strong> {{ $log->total_session ?: '-' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="inline-card">
                                <div class="inline-title">Belum ada update history.</div>
                                <div style="font-size:13px;color:#64748b;margin-top:6px;">History akan tercatat setelah Medical Record ini disimpan.</div>
                            </div>
                        @endif
                    </div>

                            <div class="mr-tab-actions">
                                <button type="button" class="mr-tab-nav-btn" data-mr-next="intervention">← Back: Intervention</button>
                                <button type="submit" class="mr-tab-nav-btn primary">Simpan Medical Record V2</button>
                            </div>
                        </div>
                    </div>

                    <div class="submit-row">
                        <button type="submit" class="submit-btn">Simpan Medical Record V2</button>
                    </div>
                </form>
                </section>
            </div>
        </div>
    </div>
</div>

<script>



    function updateGoalPhaseFields() {
        const select = document.getElementById('goalPhaseSelect');
        const helper = document.getElementById('goalPhaseHelper');
        const fields = document.querySelectorAll('[data-goal-phase-field]');

        if (!select || !fields.length) return;

        const selected = select.value || '';

        fields.forEach(function (field) {
            const isActive = selected && field.getAttribute('data-goal-phase-field') === selected;
            field.classList.toggle('is-hidden', !isActive);
        });

        if (helper) {
            helper.classList.toggle('is-visible', !selected);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateGoalPhaseFields();

        const goalPhaseSelect = document.getElementById('goalPhaseSelect');
        if (goalPhaseSelect) {
            goalPhaseSelect.addEventListener('change', updateGoalPhaseFields);
        }
    });

    function activateMedicalRecordTab(target) {
        if (!target) return;

        document.querySelectorAll('[data-mr-tab]').forEach(function (button) {
            button.classList.toggle('active', button.getAttribute('data-mr-tab') === target);
        });

        document.querySelectorAll('[data-mr-panel]').forEach(function (panel) {
            panel.classList.toggle('active', panel.getAttribute('data-mr-panel') === target);
        });

        const shell = document.querySelector('.mr-tabs-shell');
        if (shell) {
            shell.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    document.querySelectorAll('[data-mr-tab]').forEach(function (button) {
        button.addEventListener('click', function () {
            activateMedicalRecordTab(button.getAttribute('data-mr-tab'));
        });
    });

    document.querySelectorAll('[data-mr-next]').forEach(function (button) {
        button.addEventListener('click', function () {
            activateMedicalRecordTab(button.getAttribute('data-mr-next'));
        });
    });

    @php
        $homeExerciseTemplateData = ($homeExerciseTemplates ?? collect())->map(function ($template) {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'category' => $template->category,
                'target_area' => $template->target_area,
                'difficulty' => $template->difficulty,
                'instructions' => $template->instructions,
                'dosage' => $template->dosage,
                'video_url' => $template->video_url,
            ];
        })->values();
    @endphp

    const homeExerciseTemplates = @json($homeExerciseTemplateData);

    function removeRow(button) {
        const row = button.closest('.inline-card');
        if (row) row.remove();
    }

    function addHistoryRow() {
        const wrapper = document.getElementById('history-wrapper');
        const div = document.createElement('div');
        div.className = 'inline-card history-row';
        div.innerHTML = `
            <div class="inline-head">
                <div class="inline-title">History Item</div>
                <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
            </div>
            <div class="grid-3">
                <div class="field">
                    <label>History Type</label>
                    <input type="text" name="history_type[]" placeholder="injury / surgery / fracture / other">
                </div>
                <div class="field">
                    <label>History Date</label>
                    <input type="date" name="history_date[]">
                </div>
                <div class="field">
                    <label>History Note</label>
                    <input type="text" name="history_note[]" placeholder="Catatan singkat">
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    function addComorbidityRow() {
        const wrapper = document.getElementById('comorbidity-wrapper');
        const index = wrapper.querySelectorAll('.comorbidity-row').length;
        const div = document.createElement('div');
        div.className = 'inline-card comorbidity-row';
        div.innerHTML = `
            <div class="inline-head">
                <div class="inline-title">Comorbidity Item</div>
                <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
            </div>
            <div class="grid-3">
                <div class="field">
                    <label>Name</label>
                    <input type="text" name="comorbidity_name[]" placeholder="Contoh: diabetes / hipertensi">
                </div>
                <div class="field">
                    <label>Measurement Date</label>
                    <input type="date" name="comorbidity_measurement_date[]">
                </div>
                <div class="field">
                    <label>Final Value</label>
                    <input type="text" name="comorbidity_final_value[]" placeholder="Nilai akhir / hasil ukur">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Note</label>
                    <input type="text" name="comorbidity_note[]" placeholder="Catatan tambahan">
                </div>
                <div class="field">
                    <label>Checked</label>
                    <div class="checkbox-row">
                        <input type="checkbox" name="comorbidity_checked[${index}]">
                        <span>Tandai bila komorbid ini aktif / relevan</span>
                    </div>
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    function addSupportingDataRow() {
        const wrapper = document.getElementById('supporting-data-wrapper');
        const div = document.createElement('div');
        div.className = 'inline-card supporting-row';
        div.innerHTML = `
            <input type="hidden" name="supporting_data_id[]" value="">
            <div class="inline-head">
                <div class="inline-title">Supporting Data Item</div>
                <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
            </div>
            <div class="grid-3">
                <div class="field">
                    <label>Date</label>
                    <input type="date" name="supporting_data_date[]">
                </div>

                <div class="field">
                    <label>Type of Data</label>
                    <input type="text" name="supporting_data_type[]" placeholder="Contoh: X-ray / MRI / Lab">
                </div>

                <div class="field">
                    <label>Interpretation</label>
                    <input type="text" name="supporting_data_interpretation[]" placeholder="Interpretasi singkat">
                </div>

                <div class="field">
                    <label>Upload File</label>
                    <div class="file-upload-box">
                        <input type="file" name="supporting_data_file[]" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx">
                        <div class="file-upload-hint">Opsional: upload foto, PDF, atau dokumen hasil pemeriksaan.</div>
                    </div>
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    function homeExerciseTemplateOptionsHtml() {
        let html = '<option value="">Manual / pilih template</option>';

        homeExerciseTemplates.forEach(template => {
            const labelParts = [template.name];

            if (template.category) {
                labelParts.push(template.category);
            }

            html += `<option value="${template.id}">${labelParts.join(' · ')}</option>`;
        });

        return html;
    }

    function applyHomeExerciseTemplate(select) {
        const selectedId = String(select.value || '');
        if (!selectedId) return;

        const template = homeExerciseTemplates.find(item => String(item.id) === selectedId);
        if (!template) return;

        const row = select.closest('.home-exercise-row');
        if (!row) return;

        const nameInput = row.querySelector('input[name="home_exercise_name[]"]');
        const dosageInput = row.querySelector('input[name="home_exercise_dosage[]"]');
        const noteInput = row.querySelector('input[name="home_exercise_note[]"]');

        if (nameInput) {
            nameInput.value = template.name || '';
        }

        if (dosageInput) {
            dosageInput.value = template.dosage || '';
        }

        if (noteInput) {
            let note = template.instructions || '';

            if (template.target_area) {
                note += (note ? ' | ' : '') + 'Target area: ' + template.target_area;
            }

            if (template.difficulty) {
                note += (note ? ' | ' : '') + 'Difficulty: ' + template.difficulty;
            }

            if (template.video_url) {
                note += (note ? ' | ' : '') + 'Video: ' + template.video_url;
            }

            noteInput.value = note;
        }
    }

    function addHomeExerciseRow() {
        const wrapper = document.getElementById('home-exercise-wrapper');
        const div = document.createElement('div');
        div.className = 'inline-card home-exercise-row';
        div.innerHTML = `
            <div class="inline-head">
                <div class="inline-title">Home Exercise Item</div>
                <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
            </div>
            <div class="grid-3">
                <div class="field">
                    <label>Pilih dari Exercise Library</label>
                    <select onchange="applyHomeExerciseTemplate(this)">
                        ${homeExerciseTemplateOptionsHtml()}
                    </select>
                </div>
                <div class="field">
                    <label>Exercise</label>
                    <input type="text" name="home_exercise_name[]" placeholder="Nama latihan">
                </div>
                <div class="field">
                    <label>Dosage</label>
                    <input type="text" name="home_exercise_dosage[]" placeholder="Contoh: 10 reps x 3 set">
                </div>
                <div class="field" style="grid-column:1/-1;">
                    <label>Note / Caution</label>
                    <input type="text" name="home_exercise_note[]" placeholder="Catatan kehati-hatian">
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    document.addEventListener('DOMContentLoaded', function () {
        function normalizePainAreaItems(rawItems) {
            if (!Array.isArray(rawItems)) return [];

            return rawItems.map(function (item) {
                if (typeof item === 'string') {
                    return {
                        area: item,
                        intensity: '',
                        type: '',
                    };
                }

                return {
                    area: item && item.area ? item.area : '',
                    intensity: item && item.intensity !== undefined ? String(item.intensity) : '',
                    type: item && item.type !== undefined ? String(item.type) : '',
                };
            }).filter(function (item) {
                return item.area;
            });
        }

        function parsePainAreas() {
            const hidden = document.getElementById('painBodyAreas');
            if (!hidden || !hidden.value) return [];

            try {
                return normalizePainAreaItems(JSON.parse(hidden.value));
            } catch (error) {
                return normalizePainAreaItems(hidden.value.split(',').map(function (item) {
                    return item.trim();
                }).filter(Boolean));
            }
        }

        function savePainAreas(items) {
            const unique = [];
            const seen = {};

            normalizePainAreaItems(items).forEach(function (item) {
                if (seen[item.area]) {
                    return;
                }

                seen[item.area] = true;
                unique.push(item);
            });

            const hidden = document.getElementById('painBodyAreas');
            const primarySelect = document.getElementById('painBodyArea');
            const globalIntensity = document.getElementById('painBodyGlobalIntensity');
            const globalType = document.getElementById('painBodyGlobalType');

            if (hidden) {
                hidden.value = JSON.stringify(unique);
            }

            if (primarySelect) {
                primarySelect.value = unique.length ? unique[unique.length - 1].area : '';
            }

            const latestWithIntensity = [...unique].reverse().find(function (item) {
                return item.intensity !== '';
            });

            if (globalIntensity) {
                globalIntensity.value = latestWithIntensity ? latestWithIntensity.intensity : '';
            }

            const latestWithType = [...unique].reverse().find(function (item) {
                return item.type !== '';
            });

            if (globalType) {
                globalType.value = latestWithType ? latestWithType.type : '';
            }

            khayraSyncPainAreaActive();
        }

        function renderPainAreaChips(items) {
            const list = document.getElementById('selectedPainAreasList');
            if (!list) return;

            const areas = normalizePainAreaItems(items);
            list.innerHTML = '';

            if (!areas.length) {
                const empty = document.createElement('div');
                empty.className = 'pain-area-empty';
                empty.textContent = 'Klik area pada anatomy map. Area yang dipilih akan muncul di sini.';
                list.appendChild(empty);
                return;
            }

            const painTypes = ['Tajam','Tumpul','Terbakar','Kesemutan','Kebas','Tertarik','Berdenyut','Menjalar','Kaku','Spasm','Weakness'];

            areas.forEach(function (item) {
                const row = document.createElement('div');
                row.className = 'pain-area-row';

                row.innerHTML = `
                    <div>
                        <div class="pain-area-name">${item.area}</div>
                        <div class="pain-area-sub">Per-area detail</div>
                    </div>

                    <select class="pain-area-intensity" data-area-intensity="${item.area}">
                        <option value="">Intensity</option>
                        ${Array.from({ length: 11 }, function (_, index) {
                            return `<option value="${index}" ${String(item.intensity) === String(index) ? 'selected' : ''}>${index}/10</option>`;
                        }).join('')}
                    </select>

                    <select class="pain-area-type" data-area-type="${item.area}">
                        <option value="">Tipe Nyeri</option>
                        ${painTypes.map(function (type) {
                            return `<option value="${type}" ${String(item.type) === String(type) ? 'selected' : ''}>${type}</option>`;
                        }).join('')}
                    </select>

                    <button type="button" class="pain-area-remove" data-remove-area="${item.area}">×</button>
                `;

                list.appendChild(row);
            });

            list.querySelectorAll('[data-area-intensity]').forEach(function (select) {
                select.addEventListener('change', function () {
                    const area = select.getAttribute('data-area-intensity');
                    const nextItems = parsePainAreas().map(function (item) {
                        if (item.area === area) {
                            item.intensity = select.value;
                        }
                        return item;
                    });
                    savePainAreas(nextItems);
                });
            });

            list.querySelectorAll('[data-area-type]').forEach(function (select) {
                select.addEventListener('change', function () {
                    const area = select.getAttribute('data-area-type');
                    const nextItems = parsePainAreas().map(function (item) {
                        if (item.area === area) {
                            item.type = select.value;
                        }
                        return item;
                    });
                    savePainAreas(nextItems);
                });
            });

            list.querySelectorAll('[data-remove-area]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const removeArea = button.getAttribute('data-remove-area');
                    const nextItems = parsePainAreas().filter(function (item) {
                        return item.area !== removeArea;
                    });
                    savePainAreas(nextItems);
                });
            });
        }

        function khayraSyncPainAreaActive() {
            const items = parsePainAreas();
            const areaNames = items.map(function (item) {
                return item.area;
            });

            const primarySelect = document.getElementById('painBodyArea');

            document.querySelectorAll('.anatomy-region').forEach(function (region) {
                region.classList.toggle('active', areaNames.includes(region.getAttribute('data-area')));
            });

            if (primarySelect && areaNames.length && !areaNames.includes(primarySelect.value)) {
                primarySelect.value = areaNames[areaNames.length - 1];
            }

            renderPainAreaChips(items);
        }

        document.querySelectorAll('.anatomy-svg-wrap').forEach(function (wrap) {
            wrap.addEventListener('click', function (event) {
                const region = event.target.closest('.anatomy-region');

                if (!region) return;

                const area = region.getAttribute('data-area');
                if (!area) return;

                const items = parsePainAreas();
                const exists = items.some(function (item) {
                    return item.area === area;
                });

                const nextItems = exists
                    ? items.filter(function (item) { return item.area !== area; })
                    : items.concat([{ area: area, intensity: '', type: '' }]);

                savePainAreas(nextItems);
            });
        });

        const khayraPainAreaSelect = document.getElementById('painBodyArea');
        if (khayraPainAreaSelect) {
            khayraPainAreaSelect.addEventListener('change', function () {
                const selected = khayraPainAreaSelect.value;
                const items = parsePainAreas();
                const areaNames = items.map(function (item) {
                    return item.area;
                });

                if (selected && !areaNames.includes(selected)) {
                    savePainAreas(items.concat([{ area: selected, intensity: '', type: '' }]));
                } else {
                    khayraSyncPainAreaActive();
                }
            });

            khayraSyncPainAreaActive();
        }

        document.querySelectorAll('.anatomy-side-tab').forEach(function (tab) {
            tab.addEventListener('click', function () {
                const view = tab.getAttribute('data-view');

                document.querySelectorAll('.anatomy-side-tab').forEach(function (item) {
                    item.classList.toggle('active', item === tab);
                });

                document.querySelectorAll('.anatomy-svg-panel').forEach(function (panel) {
                    panel.classList.toggle('active', panel.getAttribute('data-panel') === view);
                });
            });
        });
    });

</script>
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