@extends('layouts.app')

@section('content')
<style>
    /* ── Board page background ── */
    .kanban-board-bg {
        background: #f4f5f7;
    }
    /* ── Scrollbars ── */
    .kb-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
    .kb-scroll::-webkit-scrollbar-track { background: transparent; }
    .kb-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,.15); border-radius: 4px; }
    .kb-scroll::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,.28); }
    .dark .kb-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); }

    /* ── Columns ── */
    .kb-col {
        width: 245px;
        min-width: 245px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e4e6ea;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        display: flex;
        flex-direction: column;
        height: 600px;
        max-height: 600px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .dark .kb-col {
        background: #1e2433;
        border-color: #2d3548;
    }

    /* ── Column header ── */
    .kb-col-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px 6px;
        font-size: 14px;
        font-weight: 700;
        color: #172b4d;
        flex-shrink: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        letter-spacing: -.01em;
        border-bottom: 1px solid #f1f3f5;
        margin-bottom: 2px;
    }
    .dark .kb-col-header { color: #e2e8f0; border-bottom-color: #2d3548; }

    .kb-col-header-icons { display: flex; align-items: center; gap: 6px; color: #97a0af; }
    .kb-col-header-icons svg { cursor: pointer; transition: color .15s; }
    .kb-col-header-icons svg:hover { color: #42526e; }

    /* ── Card scroll area ── */
    .kb-cards {
        flex: 1 1 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 6px 8px 8px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-height: 0;
    }

    .kb-empty-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 60px;
        border: 1.5px dashed #e4e6ea;
        border-radius: 8px;
        color: #97a0af;
        font-size: 10px;
        font-weight: 600;
        background: #fafbfc;
        transition: background 0.12s, border-color 0.12s;
        user-select: none;
        cursor: pointer;
    }
    .kb-empty-placeholder:hover {
        background: #f1f3f5;
        border-color: #cbd5e1;
        color: #4a5568;
    }
    .dark .kb-empty-placeholder {
        border-color: #2d3548;
        color: #4a5568;
        background: #171c26;
    }
    .dark .kb-empty-placeholder:hover {
        background: #1e2533;
        border-color: #4a5568;
        color: #e2e8f0;
    }

    /* ── Banner card ── */
    .kb-banner {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,.08);
        background: #fff;
        flex-shrink: 0;
    }
    .dark .kb-banner { background: #252d3d; border-color: rgba(255,255,255,.06); }

    .kb-banner-cover {
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-weight: 800;
        font-size: 14px;
        color: #fff;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        letter-spacing: -.02em;
    }
    .kb-banner-foot {
        padding: 5px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 700;
        color: #6b778c;
        border-top: 1px solid rgba(0,0,0,.05);
    }
    .kb-banner-count {
        background: #f4f5f7;
        border-radius: 20px;
        padding: 1px 8px;
        font-size: 9px;
        font-weight: 800;
        color: #42526e;
    }
    .dark .kb-banner-foot { color: #94a3b8; }
    .dark .kb-banner-count { background: #2d3548; color: #94a3b8; }

    /* ── Banner cover colors (Trello palette) ── */
    .kb-bg-new        { background-color: #0079bf; }
    .kb-bg-interested { background-color: #eb5a46; }
    .kb-bg-contacted  { background-color: #0079bf; }
    .kb-bg-inprogress { background-color: #61bd4f; }
    .kb-bg-followup   { background-color: #ff9f1a; }
    .kb-bg-onhold     { background-color: #ea526f; }
    .kb-bg-converted  { background-color: #00c2e0; }
    .kb-bg-closedwon  { background-color: #51e898; }
    .kb-bg-closedlost { background-color: #ec2f4b; }
    .kb-bg-notinterested { background-color: #5e6c84; }

    /* ── Follow Up column warm bg ── */
    .kb-col-followup, .kb-col-onhold {
        background: #fffbe6;
        border-color: #ffe58f;
    }
    .dark .kb-col-followup, .dark .kb-col-onhold {
        background: #242118;
        border-color: #403b1a;
    }

    /* ── Client card ── */
    .kb-card {
        background: #fff;
        border: 1px solid #dfe1e6;
        border-radius: 8px;
        padding: 9px 10px;
        cursor: grab;
        transition: box-shadow .15s, transform .15s;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .kb-card:active { cursor: grabbing; }
    .kb-card:hover  { box-shadow: 0 3px 10px rgba(0,0,0,.1); transform: translateY(-1px); border-color: #c1c7d0; }
    .dark .kb-card  { background: #252d3d; border-color: #2d3548; }
    .dark .kb-card:hover { border-color: #3d4a63; }

    .kb-card-name {
        font-size: 13px;
        font-weight: 700;
        color: #172b4d;
        line-height: 1.35;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .dark .kb-card-name { color: #e2e8f0; }
    .kb-card-sub {
        font-size: 11px;
        color: #6b778c;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .kb-card-meta {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        color: #6b778c;
        margin-top: 5px;
        white-space: nowrap;
        overflow: hidden;
    }
    .kb-card-meta span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .kb-card-meta svg { flex-shrink: 0; }
    .kb-card-footer {
        margin-top: 8px;
        padding-top: 7px;
        border-top: 1px solid #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 5px;
        font-size: 11px;
        color: #6b778c;
        white-space: nowrap;
    }
    .dark .kb-card-footer { border-color: #2d3548; }

    .kb-overdue {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #ffebe6;
        color: #de350b;
        font-size: 10px;
        font-weight: 700;
        padding: 2.5px 7px;
        border-radius: 20px;
    }
    .dark .kb-overdue { background: rgba(222,53,11,.15); }

    .kb-badge {
        display: inline-block;
        background: rgba(0,121,191,.08);
        color: #0065a2;
        font-size: 10px;
        font-weight: 700;
        padding: 2.5px 7px;
        border-radius: 4px;
        margin-bottom: 5px;
        letter-spacing: .01em;
    }
    .dark .kb-badge { background: rgba(0,121,191,.2); color: #58aee8; }

    .kb-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
    }

    /* drag hover */
    .kb-drag-over {
        outline: 2px dashed rgba(0,121,191,.4);
        outline-offset: 2px;
        background: rgba(0,121,191,.03);
        border-radius: 8px;
    }

    /* ── Board wrapper ── */
    .kb-board-wrapper {
        position: relative;
        width: 100%;
    }

    .kb-board-scroll {
        overflow-x: auto;
        overflow-y: visible;
        padding: 16px 8px 20px;
        background: #f4f5f7;
        border-radius: 14px;
        border: 1px solid #e4e6ea;
        scroll-behavior: smooth;
    }
    .dark .kb-board-scroll {
        background: #0f1623;
        border-color: #1e2433;
    }
    .kb-board-scroll::-webkit-scrollbar { height: 5px; }
    .kb-board-scroll::-webkit-scrollbar-track { background: transparent; border-radius: 4px; }
    .kb-board-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,.15); border-radius: 4px; }
    .kb-board-scroll::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,.28); }
    .dark .kb-board-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); }

    .kb-board-inner {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        min-width: max-content;
    }

    /* Scroll arrow buttons */
    .kb-scroll-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #dfe1e6;
        box-shadow: 0 2px 8px rgba(0,0,0,.12);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #42526e;
        transition: background .12s, box-shadow .12s, opacity .2s;
        opacity: 0.85;
    }
    .kb-scroll-btn:hover { background: #f4f5f7; box-shadow: 0 4px 14px rgba(0,0,0,.16); opacity: 1; }
    .kb-scroll-btn--left  { left: -14px; }
    .kb-scroll-btn--right { right: -14px; }
    .dark .kb-scroll-btn {
        background: #1e2433;
        border-color: #2d3548;
        color: #94a3b8;
    }
    .dark .kb-scroll-btn:hover { background: #252d3d; }

    /* ── Filter bar ── */
    .kb-filter-bar {
        background: #ffffff;
        border: 1px solid #e4e6ea;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        padding: 12px 16px 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        position: relative;
        overflow: hidden;
    }
    .kb-filter-bar::before {
        content: '';
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #0079bf 0%, #00c2e0 100%);
        border-radius: 14px 0 0 14px;
    }
    .dark .kb-filter-bar {
        background: #1e2433;
        border-color: #2d3548;
        box-shadow: 0 1px 6px rgba(0,0,0,.25);
    }

    .kb-filter-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 10.5px;
        font-weight: 700;
        color: #0079bf;
        letter-spacing: .04em;
        text-transform: uppercase;
        padding-left: 4px;
    }
    .dark .kb-filter-label { color: #58aee8; }

    .kb-filter-inputs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
    }
    @media (max-width: 600px) {
        .kb-filter-inputs { grid-template-columns: 1fr; }
    }

    /* individual field wrapper */
    .kb-filter-field {
        position: relative;
        display: flex;
        align-items: center;
        background: #f7f8fa;
        border: 1.5px solid #e4e6ea;
        border-radius: 9px;
        transition: border-color .15s, box-shadow .15s;
        height: 36px;
        overflow: hidden;
    }
    .kb-filter-field:focus-within {
        border-color: #0079bf;
        box-shadow: 0 0 0 3px rgba(0,121,191,.12);
        background: #fff;
    }
    .dark .kb-filter-field {
        background: #252d3d;
        border-color: #2d3548;
    }
    .dark .kb-filter-field:focus-within {
        border-color: #58aee8;
        box-shadow: 0 0 0 3px rgba(88,174,232,.12);
        background: #1e2433;
    }

    .kb-filter-field--info {
        background: #f0f7ff;
        border-color: #bfdbfe;
        color: #2563eb;
        font-size: 11px;
        font-weight: 500;
        gap: 7px;
        padding: 0 12px;
        cursor: default;
    }
    .dark .kb-filter-field--info {
        background: rgba(37,99,235,.1);
        border-color: rgba(37,99,235,.3);
        color: #93c5fd;
    }

    .kb-filter-icon {
        color: #97a0af;
        flex-shrink: 0;
        margin-left: 10px;
        margin-right: 0;
        pointer-events: none;
    }
    .kb-filter-field:focus-within .kb-filter-icon { color: #0079bf; }
    .dark .kb-filter-field:focus-within .kb-filter-icon { color: #58aee8; }

    .kb-filter-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        font-size: 11.5px;
        color: #172b4d;
        padding: 0 8px;
        height: 100%;
        min-width: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .kb-filter-input::placeholder { color: #b0b7c3; }
    .dark .kb-filter-input { color: #e2e8f0; }
    .dark .kb-filter-input::placeholder { color: #4a5568; }

    /* Select-specific overrides */
    .kb-filter-select {
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        padding-right: 28px;
    }
    .dark .kb-filter-select { background: transparent; }
    .dark .kb-filter-select option { background: #1e2433; color: #e2e8f0; }

    .kb-select-chevron {
        position: absolute;
        right: 10px;
        color: #97a0af;
        pointer-events: none;
        flex-shrink: 0;
    }

    /* Clear × button */
    .kb-filter-clear {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: none;
        background: #dfe1e6;
        color: #42526e;
        cursor: pointer;
        flex-shrink: 0;
        margin-right: 8px;
        transition: background .12s, color .12s;
    }
    .kb-filter-clear:hover { background: #c1c7d0; color: #172b4d; }
    .dark .kb-filter-clear { background: #2d3548; color: #94a3b8; }
    .dark .kb-filter-clear:hover { background: #3d4a63; color: #e2e8f0; }

    /* Active filter badge row */
    .kb-filter-badges {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        padding-left: 4px;
    }

    .kb-filter-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(0,121,191,.08);
        color: #0065a2;
        border: 1px solid rgba(0,121,191,.2);
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        padding: 3px 10px 3px 10px;
        line-height: 1;
    }
    .dark .kb-filter-badge {
        background: rgba(88,174,232,.1);
        color: #58aee8;
        border-color: rgba(88,174,232,.25);
    }

    .kb-badge-remove {
        background: none;
        border: none;
        cursor: pointer;
        color: inherit;
        opacity: .6;
        font-size: 13px;
        line-height: 1;
        padding: 0 1px;
        display: inline-flex;
        align-items: center;
    }
    .kb-badge-remove:hover { opacity: 1; }

    .kb-clear-all {
        background: none;
        border: 1px solid #dfe1e6;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        color: #de350b;
        padding: 3px 10px;
        cursor: pointer;
        transition: background .12s, border-color .12s;
    }
    .kb-clear-all:hover { background: #ffebe6; border-color: #de350b; }
    .dark .kb-clear-all { border-color: #3d4a63; color: #fc8181; }
    .dark .kb-clear-all:hover { background: rgba(252,129,129,.08); border-color: #fc8181; }

    /* ── Card Detail Modal ── */
    .kdm-overlay {
        position: fixed; inset: 0;
        background: rgba(9,14,26,.45);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        animation: kdm-fade .24s ease;
        overflow-y: auto;
    }
    @keyframes kdm-fade { from { opacity:0 } to { opacity:1 } }

    .kdm-panel {
        background: #fff;
        width: 100%;
        max-width: 1100px;
        max-height: 90vh;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,.18);
        display: flex;
        flex-direction: column;
        animation: kdm-slide .32s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
        position: relative;
    }
    @keyframes kdm-slide {
        from { transform: translateY(30px) scale(0.97); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }
    .dark .kdm-panel { background: #1a202c; box-shadow: 0 20px 50px rgba(0,0,0,.35); }

    .kdm-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 32px;
        border-bottom: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .dark .kdm-header { border-color: #2d3748; }

    .kdm-avatar {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; font-weight: 800; color: #fff;
        flex-shrink: 0;
    }
    .kdm-title {
        flex: 1;
        font-size: 18px;
        font-weight: 700;
        color: #1a202c;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .dark .kdm-title { color: #e2e8f0; }
    .kdm-status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 10.5px; font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        background: rgba(0,121,191,.1);
        color: #0065a2;
    }
    .dark .kdm-status-pill { background: rgba(88,174,232,.15); color: #58aee8; }
    .kdm-close {
        width: 32px; height: 32px; border-radius: 50%;
        border: none; background: #f4f5f7;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: #42526e;
        transition: background .12s;
        flex-shrink: 0;
    }
    .kdm-close:hover { background: #dfe1e6; }
    .dark .kdm-close { background: #2d3748; color: #94a3b8; }
    .dark .kdm-close:hover { background: #4a5568; }

    .kdm-body {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 0;
        flex: 1;
        overflow: hidden;
    }

    /* Modal section card styling (matching Add Form sections) */
    .kdm-section-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    .dark .kdm-section-card {
        background: #1e2530;
        border-color: #2d3748;
        box-shadow: none;
    }
    .kdm-section-header-new {
        padding: 14px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #fcfdfe;
    }
    .dark .kdm-section-header-new {
        border-color: #2d3748;
        background: #252d3d;
    }
    .kdm-section-title-new {
        font-size: 13px;
        font-weight: 700;
        color: #1a202c;
        margin: 0;
    }
    .dark .kdm-section-title-new {
        color: #e2e8f0;
    }
    .kdm-section-desc {
        font-size: 11px;
        color: #718096;
        margin: 2px 0 0 0;
    }
    .dark .kdm-section-desc {
        color: #a0aec0;
    }
    .kdm-section-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .kdm-main {
        padding: 24px 32px;
        border-right: 1px solid #e2e8f0;
        overflow-y: auto;
        box-sizing: border-box;
    }
    .dark .kdm-main { border-color: #2d3748; }
    .kdm-side {
        padding: 24px 32px;
        background: #f7fafc;
        overflow-y: auto;
        box-sizing: border-box;
    }
    .dark .kdm-side { background: #171923; }

    @media (max-width: 800px) {
        .kdm-header {
            padding: 20px 24px;
        }
        .kdm-body {
            grid-template-columns: 1fr;
            overflow-y: auto;
        }
        .kdm-main {
            padding: 24px;
            border-right: none;
            overflow-y: visible;
            height: auto;
        }
        .kdm-side {
            padding: 24px;
            overflow-y: visible;
            height: auto;
        }
    }

    .kdm-section-title {
        font-size: 10px; font-weight: 700; letter-spacing: .06em;
        text-transform: uppercase; color: #97a0af;
        margin-bottom: 10px; margin-top: 18px;
    }
    .kdm-section-title:first-child { margin-top: 0; }

    .kdm-field {
        margin-bottom: 10px;
    }
    .kdm-label {
        font-size: 10px; font-weight: 600; color: #6b778c;
        margin-bottom: 3px; display: block;
    }
    .dark .kdm-label { color: #94a3b8; }
    .kdm-input {
        width: 100%; background: #fff;
        border: 1.5px solid #dfe1e6;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 12px; color: #172b4d;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }
    .kdm-input:focus {
        outline: none;
        border-color: #0079bf;
        box-shadow: 0 0 0 3px rgba(0,121,191,.1);
    }
    .dark .kdm-input {
        background: #252d3d; border-color: #2d3548;
        color: #e2e8f0;
    }
    .dark .kdm-input:focus { border-color: #58aee8; box-shadow: 0 0 0 3px rgba(88,174,232,.1); }

    .kdm-textarea { min-height: 90px; resize: vertical; }

    .kdm-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding: 20px 30px;
        border-top: 1px solid #e2e8f0;
        background: #f7fafc;
        flex-shrink: 0;
    }
    .dark .kdm-footer { border-color: #2d3748; background: #171923; }

    @media (max-width: 800px) {
        .kdm-footer {
            padding: 16px 24px;
        }
    }

    .kdm-btn-save {
        display: inline-flex; align-items: center; gap: 6px;
        background: #0079bf; color: #fff;
        border: none; border-radius: 8px;
        padding: 8px 18px;
        font-size: 12px; font-weight: 700;
        cursor: pointer;
        transition: background .14s, transform .12s;
    }
    .kdm-btn-save:hover { background: #006aa3; transform: translateY(-1px); }
    .kdm-btn-save:disabled { background: #97a0af; cursor: not-allowed; transform: none; }

    .kdm-btn-view {
        display: inline-flex; align-items: center; gap: 6px;
        background: transparent; color: #42526e;
        border: 1.5px solid #dfe1e6;
        border-radius: 8px; padding: 7px 14px;
        font-size: 12px; font-weight: 600;
        cursor: pointer; text-decoration: none;
        transition: border-color .12s, background .12s;
    }
    .kdm-btn-view:hover { border-color: #0079bf; color: #0079bf; background: rgba(0,121,191,.04); }
    .dark .kdm-btn-view { color: #94a3b8; border-color: #2d3548; }
    .dark .kdm-btn-view:hover { color: #58aee8; border-color: #58aee8; }

    .kdm-saving-spinner {
        width: 13px; height: 13px;
        border: 2px solid rgba(255,255,255,.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .6s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .kdm-overdue-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: #ffebe6; color: #de350b;
        border-radius: 20px; padding: 3px 10px;
        font-size: 10px; font-weight: 700;
    }
    .flatpickr-calendar {
        z-index: 999999 !important;
    }
    [x-cloak] { display: none !important; }
</style>

<div x-data="{
    clients: {{ $clients->map(function($c) {
        return [
            'id'                  => $c->id,
            'name'                => $c->name,
            'email'               => $c->email ?? '',
            'mobile_no'           => $c->mobile_no ?? '',
            'website'             => $c->website ?? '',
            'linkedin'            => $c->linkedin ?? '',
            'facebook'            => $c->facebook ?? '',
            'instagram'           => $c->instagram ?? '',
            'youtube'             => $c->youtube ?? '',
            'x'                   => $c->x ?? '',
            'telegram'            => $c->telegram ?? '',
            'whatsapp'            => $c->whatsapp ?? '',
            'teams'               => $c->teams ?? '',
            'source_url'          => $c->source_url ?? '',
            'project_link'        => $c->project_link ?? '',
            'technology'          => $c->technology ?? '',
            'location'            => $c->location ?? '',
            'status'              => $c->status,
            'is_overdue'          => $c->isFollowUpOverdue(),
            'last_contacted'      => $c->last_contacted_date ? $c->last_contacted_date->format('d M Y') : '',
            'last_contacted_raw'  => $c->last_contacted_date ? $c->last_contacted_date->format('Y-m-d') : '',
            'next_followup'       => $c->next_follow_up_date ? $c->next_follow_up_date->format('d M Y') : '',
            'next_followup_raw'   => $c->next_follow_up_date ? $c->next_follow_up_date->format('Y-m-d') : '',
            'follow_up_days'      => $c->follow_up_days ?? '',
            'notes'               => $c->notes ?? '',
            'assigned_user'       => $c->assignedUser ? $c->assignedUser->name : 'Unassigned',
            'assigned_to'         => $c->assigned_to,
            'initials'            => collect(explode(' ', $c->name))->map(fn($n) => mb_substr($n,0,1))->take(2)->join(''),
            'show_url'            => route('clients.show', $c),
            'edit_url'            => route('clients.edit', $c),
            'quick_update_url'    => route('kanban.quick-update', $c),
        ];
    })->toJson() }},
    users: {{ collect($users)->map(fn($u) => ['id'=>$u->id,'name'=>$u->name])->toJson() }},
    allCountries: {{ json_encode(App\Helpers\CountryHelper::getAllCountries()) }},

    statuses: {{ json_encode($statuses) }},
    draggingId: null,
    overStatus: null,
    search: '',
    techFilter: '',
    assignedFilter: '',
    dateFilter: '',
    dateStart: null,
    dateEnd: null,
    datePickerInstance: null,

    /* Modal state */
    cardModal: false,
    modalClient: null,
    modalForm: {},
    modalSaving: false,
    selectedTemplateId: '',
    templates: [],

    /* Create Client Modal state */
    createModal: false,
    createForm: {},
    createSaving: false,

    slug(s){ return s.toLowerCase().replace(/\s+/g,''); },

    emoji(s){
        return {'New':'📥','Interested':'🤔','Contacted':'📞','In Progress':'⚙️',
                'Follow Up':'⏰','On Hold':'⏸️','Converted':'🎉',
                'Closed Won':'🏆','Closed Lost':'❌','Not Interested':'👎'}[s] || '📋';
    },

    avatarBg(name){
        const c=['#0079bf','#61bd4f','#eb5a46','#ff9f1a','#ea526f','#00c2e0','#51e898','#5e6c84'];
        let h=0; for(let i=0;i<name.length;i++) h=name.charCodeAt(i)+((h<<5)-h);
        return c[Math.abs(h)%c.length];
    },

    init() {
        this.$nextTick(() => {
            if (window.flatpickr) {
                this.datePickerInstance = window.flatpickr('.kb-filter-date-picker', {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    placeholder: 'Filter by next follow-up...',
                    onChange: (selectedDates, dateStr) => {
                        this.dateFilter = dateStr;
                        if (selectedDates.length === 2) {
                            this.dateStart = selectedDates[0];
                            this.dateEnd = selectedDates[1];
                        } else if (selectedDates.length === 1) {
                            this.dateStart = selectedDates[0];
                            this.dateEnd = selectedDates[0];
                        } else {
                            this.dateStart = null;
                            this.dateEnd = null;
                        }
                    }
                });
            }
        });

        // Load templates
        axios.get('{{ route('templates.index') }}')
            .then(res => {
                this.templates = res.data.templates || [];
            })
            .catch(err => {
                console.error('Failed to load templates:', err);
            });
    },

    clearDateFilter() {
        this.dateFilter = '';
        this.dateStart = null;
        this.dateEnd = null;
        if (this.datePickerInstance) {
            this.datePickerInstance.clear();
        }
    },

    filtered(status){
        return this.clients.filter(c=>{
            if(c.status!==status) return false;
            if(this.search){
                const t=this.search.toLowerCase();
                if(!c.name.toLowerCase().includes(t)&&!c.email.toLowerCase().includes(t)&&
                   !c.technology.toLowerCase().includes(t)&&!c.location.toLowerCase().includes(t)) return false;
            }
            if(this.techFilter && !c.technology.toLowerCase().includes(this.techFilter.toLowerCase())) return false;
            if(this.assignedFilter && c.assigned_to!=this.assignedFilter) return false;

            if (this.dateStart && this.dateEnd) {
                if (!c.next_followup_raw) return false;
                // Parse date comparison safely
                const clientDate = new Date(c.next_followup_raw);
                clientDate.setHours(0,0,0,0);
                const start = new Date(this.dateStart); start.setHours(0,0,0,0);
                const end = new Date(this.dateEnd); end.setHours(0,0,0,0);
                if (clientDate < start || clientDate > end) return false;
            }
            return true;
        });
    },

    dragStart(e,id){
        this.draggingId=id;
        e.dataTransfer.setData('text/plain',id);
        setTimeout(()=>{ const el=document.getElementById('kc-'+id); if(el) el.style.opacity='.4'; },0);
    },
    dragEnd(e,id){
        const el=document.getElementById('kc-'+id); if(el) el.style.opacity='1';
        this.draggingId=null; this.overStatus=null;
    },
    dragOver(e,s){ e.preventDefault(); this.overStatus=s; },
    dragLeave(){ this.overStatus=null; },
    drop(e,status){
        e.preventDefault(); this.overStatus=null;
        if(!this.draggingId) return;
        const id=this.draggingId;
        const client=this.clients.find(c=>c.id==id);
        if(!client||client.status===status) return;
        const old=client.status; client.status=status;
        axios.post('{{ route('kanban.update-status') }}',{client_id:id,status})
            .then(()=>window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Status updated successfully.', type: 'success' } })))
            .catch(err=>{ client.status=old; window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.error || 'Permission denied', type: 'error' } })); });
    },

    deleteClient(id,name){
        Swal.fire({title:'Delete Client?',text:`Delete ${name}?`,icon:'warning',showCancelButton:true,
            confirmButtonColor:'#ef4444',confirmButtonText:'Yes, delete!'})
        .then(r=>{ if(r.isConfirmed){ const f=document.getElementById('del-form'); f.action='/clients/'+id; f.submit(); } });
    },

    getCalculatedFollowUp() {
        if (!this.modalForm.last_contacted_date) return '';
        const parts = this.modalForm.last_contacted_date.split('-');
        if (parts.length !== 3) return '';
        
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        
        const d = new Date(year, month, day);
        if (isNaN(d.getTime())) return '';
        
        const days = parseInt(this.modalForm.follow_up_days, 10);
        if (isNaN(days) || days < 0) return '';
        
        d.setDate(d.getDate() + days);
        
        return d.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    },

    openCard(client){
        console.log('openCard called for client:', client);
        this.selectedTemplateId = '';
        this.modalClient = client;
        this.modalForm = {
            name:               client.name || '',
            email:              client.email || '',
            mobile_no:          client.mobile_no || '',
            location:           client.location || '',
            technology:         client.technology || '',
            status:             client.status || '',
            last_contacted_date:client.last_contacted_raw || '',
            follow_up_days:     client.follow_up_days || '',
            notes:              client.notes || '',
            assigned_to:        client.assigned_to || '',
            website:            client.website || '',
            linkedin:           client.linkedin || '',
            facebook:           client.facebook || '',
            instagram:          client.instagram || '',
            youtube:            client.youtube || '',
            x:                  client.x || '',
            telegram:           client.telegram || '',
            whatsapp:           client.whatsapp || '',
            teams:              client.teams || '',
            source_url:         client.source_url || '',
            project_link:       client.project_link || '',
        };
        this.cardModal = true;
        console.log('cardModal is now:', this.cardModal);
        document.body.style.overflow = 'hidden';

        this.$nextTick(() => {
            if (window.flatpickr) {
                window.flatpickr('.kdm-date-picker', {
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    defaultDate: this.modalForm.last_contacted_date,
                    onChange: (selectedDates, dateStr) => {
                        this.modalForm.last_contacted_date = dateStr;
                    }
                });
            }
        });
    },

    closeCard(){
        console.log('closeCard called');
        this.selectedTemplateId = '';
        this.cardModal = false;
        this.modalClient = null;
        document.body.style.overflow = '';
    },

    getRenderedTemplate() {
        if (!this.selectedTemplateId) return null;
        const template = this.templates.find(t => t.id == this.selectedTemplateId);
        if (!template) return null;

        let content = template.content || '';
        let subject = template.subject || '';

        // Replace placeholders
        const replacements = {
            '{client_name}': this.modalForm.name || 'Client',
            '{client_email}': this.modalForm.email || '',
            '{technology}': this.modalForm.technology || '',
            '{project_link}': this.modalForm.project_link || '',
            '{assigned_user}': '{{ auth()->user()->name }}',
        };

        for (const [placeholder, value] of Object.entries(replacements)) {
            content = content.split(placeholder).join(value);
            if (subject) {
                subject = subject.split(placeholder).join(value);
            }
        }

        return { subject, content, type: template.type };
    },

    copyToClipboard(text) {
        if (!navigator.clipboard) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
        } else {
            navigator.clipboard.writeText(text);
        }
        
        // Show success alert
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Copied to clipboard!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    },

    async saveCard(){
        if(this.modalSaving) return;
        this.modalSaving = true;
        try {
            const res = await axios.patch(this.modalClient.quick_update_url, this.modalForm);
            const updated = res.data.client;
            const idx = this.clients.findIndex(c => c.id === this.modalClient.id);
            if(idx > -1){
                Object.assign(this.clients[idx], {
                    name:              updated.name,
                    email:             updated.email,
                    mobile_no:         updated.mobile_no,
                    location:          updated.location,
                    technology:        updated.technology,
                    status:            updated.status,
                    last_contacted:    updated.last_contacted_date ? new Date(updated.last_contacted_date).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) : '',
                    last_contacted_raw:updated.last_contacted_date || '',
                    next_followup:     updated.next_followup || '',
                    next_followup_raw: updated.next_followup_raw || '',
                    follow_up_days:    updated.follow_up_days,
                    notes:             updated.notes,
                    assigned_to:       updated.assigned_to,
                    assigned_user:     updated.assigned_user,
                    website:           updated.website,
                    linkedin:          updated.linkedin,
                    facebook:          updated.facebook,
                    instagram:         updated.instagram,
                    youtube:           updated.youtube,
                    x:                 updated.x,
                    telegram:          updated.telegram,
                    whatsapp:          updated.whatsapp,
                    teams:             updated.teams,
                    source_url:        updated.source_url,
                    project_link:      updated.project_link,
                    is_overdue:        updated.is_overdue,
                });
                this.modalClient = this.clients[idx];
            }
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Client updated successfully.', type: 'success' } }));
            this.closeCard();
        } catch(err) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.message || 'Could not save', type: 'error' } }));
        } finally {
            this.modalSaving = false;
        }
    },

    openCreateModal(defaultStatus = 'New') {
        this.createForm = {
            name: '',
            email: '',
            mobile_no: '',
            location: '',
            technology: '',
            status: defaultStatus,
            last_contacted_date: '',
            follow_up_days: 7,
            notes: '',
            assigned_to: '',
            website: '',
            linkedin: '',
            facebook: '',
            instagram: '',
            youtube: '',
            x: '',
            telegram: '',
            whatsapp: '',
            teams: '',
            source_url: '',
            project_link: '',
        };
        this.createModal = true;
        document.body.style.overflow = 'hidden';

        this.$nextTick(() => {
            if (window.flatpickr) {
                window.flatpickr('.kdm-create-date-picker', {
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    onChange: (selectedDates, dateStr) => {
                        this.createForm.last_contacted_date = dateStr;
                    }
                });
            }
        });
    },

    closeCreateModal() {
        this.createModal = false;
        document.body.style.overflow = '';
    },

    getCalculatedCreateFollowUp() {
        if (!this.createForm.last_contacted_date) return '';
        const parts = this.createForm.last_contacted_date.split('-');
        if (parts.length !== 3) return '';
        
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        
        const d = new Date(year, month, day);
        if (isNaN(d.getTime())) return '';
        
        const days = parseInt(this.createForm.follow_up_days, 10);
        if (isNaN(days) || days < 0) return '';
        
        d.setDate(d.getDate() + days);
        
        return d.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    },

    async saveNewClient() {
        if (!this.createForm.name) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Client name is required.', type: 'error' } }));
            return;
        }
        if (this.createSaving) return;
        this.createSaving = true;
        try {
            const res = await axios.post('{{ route('clients.store') }}', this.createForm);
            const newClient = res.data.client;
            this.clients.unshift(newClient);
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Client created successfully.', type: 'success' } }));
            this.closeCreateModal();
        } catch(err) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.response?.data?.message || 'Could not save client', type: 'error' } }));
        } finally {
            this.createSaving = false;
        }
    }
}" class="flex flex-col gap-4 h-full">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <x-common.page-breadcrumb pageTitle="Clients Board" />
        @if(auth()->check())
        <button type="button" @click="openCreateModal()"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 transition">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M5 10h10M10 5v10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            Add Client
        </button>
        @endif
    </div>

    {{-- Filters --}}
    <div class="kb-filter-bar">
        <div class="kb-filter-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <span>Filters</span>
        </div>
        <div class="kb-filter-inputs">
            {{-- Search --}}
            <div class="kb-filter-field">
                <svg class="kb-filter-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" x-model="search" placeholder="Search clients…" class="kb-filter-input">
                <button x-show="search" @click="search=''" class="kb-filter-clear" title="Clear">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Technology --}}
            <div class="kb-filter-field">
                <svg class="kb-filter-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                </svg>
                <input type="text" x-model="techFilter" placeholder="Filter by technology…" class="kb-filter-input">
                <button x-show="techFilter" @click="techFilter=''" class="kb-filter-clear" title="Clear">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Team Member --}}
            @if(auth()->user()->role !== 'employee')
            <div class="kb-filter-field" x-data="{
                open: false,
                search: '',
                get filteredUsers() {
                    let list = [{id: '', name: 'All Team Members'}, ...users];
                    if (!this.search) return list;
                    return list.filter(u => u.name.toLowerCase().includes(this.search.toLowerCase()));
                },
                getUserName() {
                    let found = users.find(u => u.id == assignedFilter);
                    return found ? found.name : 'All Team Members';
                },
                select(id) {
                    assignedFilter = id;
                    this.open = false;
                    this.search = '';
                }
            }">
                <svg class="kb-filter-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                    class="kb-filter-input text-left cursor-pointer flex items-center justify-between"
                    style="height: 100%; border: none; background: transparent; padding-right: 28px; width: 100%;">
                    <span x-text="getUserName()"></span>
                </button>
                <svg class="kb-select-chevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                    style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column; top: 100%;">
                    <div class="mb-1.5 flex-shrink-0">
                        <input type="text" x-model="search" x-ref="searchInput" placeholder="Search user..."
                            class="kdm-input w-full" style="height: auto;">
                    </div>
                    <ul class="overflow-y-auto space-y-0.5 text-xs text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                        <template x-for="u in filteredUsers" :key="u.id">
                            <li @click="select(u.id)"
                                class="cursor-pointer rounded px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                :class="assignedFilter == u.id ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                <span x-text="u.name"></span>
                                <svg x-show="assignedFilter == u.id" class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </li>
                        </template>
                        <template x-if="filteredUsers.length === 0">
                            <li class="px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 italic">No matches</li>
                        </template>
                    </ul>
                </div>
            </div>
            @else
            <div class="kb-filter-field kb-filter-field--info">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span>Employee view — your clients only</span>
            </div>
            @endif

            {{-- Date Range Filter --}}
            <div class="kb-filter-field">
                <svg class="kb-filter-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <input type="text" x-model="dateFilter" placeholder="Filter by follow-up range…" class="kb-filter-input kb-filter-date-picker">
                <button x-show="dateFilter" @click="clearDateFilter()" class="kb-filter-clear" title="Clear">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        {{-- Active filter badges --}}
        <div class="kb-filter-badges" x-show="search || techFilter || assignedFilter || dateFilter">
            <template x-if="search">
                <span class="kb-filter-badge">Search: <strong x-text="search"></strong> <button @click="search=''" class="kb-badge-remove">×</button></span>
            </template>
            <template x-if="techFilter">
                <span class="kb-filter-badge">Tech: <strong x-text="techFilter"></strong> <button @click="techFilter=''" class="kb-badge-remove">×</button></span>
            </template>
            <template x-if="assignedFilter">
                <span class="kb-filter-badge">Member assigned <button @click="assignedFilter=''" class="kb-badge-remove">×</button></span>
            </template>
            <template x-if="dateFilter">
                <span class="kb-filter-badge">Date: <strong x-text="dateFilter"></strong> <button @click="clearDateFilter()" class="kb-badge-remove">×</button></span>
            </template>
            <button @click="search=''; techFilter=''; assignedFilter=''; clearDateFilter();" class="kb-clear-all">Clear all</button>
        </div>
    </div>

    {{-- Board --}}
    <div class="kb-board-wrapper" id="kb-wrapper">
        <button class="kb-scroll-btn kb-scroll-btn--left" id="kb-left" onclick="document.getElementById('kb-scroll').scrollBy({left:-280,behavior:'smooth'})" title="Scroll left">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button class="kb-scroll-btn kb-scroll-btn--right" id="kb-right" onclick="document.getElementById('kb-scroll').scrollBy({left:280,behavior:'smooth'})" title="Scroll right">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <div class="kb-board-scroll kb-scroll" id="kb-scroll">
            <div class="kb-board-inner">
                <template x-for="status in statuses" :key="status">
                    <div class="kb-col" :class="(slug(status)==='followup'||slug(status)==='onhold') ? 'kb-col-followup' : ''">
                        
                        {{-- Column header --}}
                        <div class="kb-col-header">
                            <span x-text="status"></span>
                            <div class="kb-col-header-icons">
                                @if(auth()->check())
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"
                                     @click="openCreateModal(status)"
                                     style="cursor:pointer;margin-right:2px;">
                                    <title>Add client to this column</title>
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                                @endif
                                <!-- <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/>
                                </svg> -->
                            </div>
                        </div>

                        {{-- Scrollable card area --}}
                        <div class="kb-cards kb-scroll"
                            @dragover="dragOver($event, status)"
                            @dragleave="dragLeave()"
                            @drop="drop($event, status)"
                            :class="overStatus===status ? 'kb-drag-over' : ''">

                            {{-- Banner cover card --}}
                            <div class="kb-banner">
                                <div class="kb-banner-cover" :class="'kb-bg-' + slug(status)">
                                    <span x-text="emoji(status)"></span>
                                    <span x-text="status"></span>
                                </div>
                                <div class="kb-banner-foot">
                                    <span x-text="status"></span>
                                    <span class="kb-banner-count" x-text="filtered(status).length"></span>
                                </div>
                            </div>

                            {{-- Client cards --}}
                            <template x-for="client in filtered(status)" :key="client.id">
                                <div :id="'kc-'+client.id"
                                     class="kb-card"
                                     :title="client.name"
                                     draggable="true"
                                     @dragstart="dragStart($event, client.id)"
                                     @dragend="dragEnd($event, client.id)"
                                     @click="openCard(client)"
                                     style="cursor:pointer;">

                                    {{-- Avatar + Name row --}}
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:5px;">
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <div class="kb-avatar"
                                                 :style="'background:'+avatarBg(client.name)"
                                                 x-text="client.initials"></div>
                                            <div>
                                                <div class="kb-card-name" :title="client.name" x-text="client.name"></div>
                                                <div class="kb-card-sub" x-text="client.assigned_user"></div>
                                            </div>
                                        </div>
                                        {{-- Actions --}}
                                        <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;" title="">
                                            {{-- Edit Button --}}
                                            <!-- <a :href="client.edit_url"
                                               @click.stop
                                               :title="'Edit Client'"
                                               style="color:#97a0af;padding:2px;display:flex;align-items:center;transition:color 0.1s;"
                                               onmouseover="this.style.color='#0079bf'"
                                               onmouseout="this.style.color='#97a0af'">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><title>Edit Client</title>
                                                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                                </svg>
                                            </a> -->

                                            {{-- Delete Button --}}
                                            <button @click.stop="deleteClient(client.id, client.name)"
                                                    :title="'Delete Client'"
                                                    style="background:none;border:none;cursor:pointer;color:#97a0af;padding:2px;display:flex;align-items:center;transition:color 0.1s;"
                                                    onmouseover="this.style.color='#de350b'"
                                                    onmouseout="this.style.color='#97a0af'">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><title>Delete Client</title>
                                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                                                </svg>
                                            </button>

                                            {{-- Dropdown Menu --}}
                                            <!-- <div x-data="{open:false}" style="position:relative;">
                                                <button @click.stop="open=!open" style="background:none;border:none;cursor:pointer;color:#97a0af;padding:2px;display:flex;align-items:center;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                        <circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/>
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open=false" x-cloak
                                                    style="position:absolute;right:0;top:20px;z-index:50;background:#fff;border:1px solid #e4e6ea;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);width:130px;padding:4px;">
                                                    <a :href="client.show_url" @click.stop style="display:block;padding:6px 12px;font-size:11px;color:#42526e;text-decoration:none;border-radius:5px;" onmouseover="this.style.background='#f4f5f7'" onmouseout="this.style.background=''">View Detail</a>
                                                    <a :href="client.edit_url" @click.stop style="display:block;padding:6px 12px;font-size:11px;color:#42526e;text-decoration:none;border-radius:5px;" onmouseover="this.style.background='#f4f5f7'" onmouseout="this.style.background=''">Edit Client</a>
                                                    <button @click.stop="deleteClient(client.id,client.name)" style="width:100%;text-align:left;padding:6px 12px;font-size:11px;color:#de350b;background:none;border:none;cursor:pointer;border-radius:5px;" onmouseover="this.style.background='#ffebe6'" onmouseout="this.style.background=''">Delete</button>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>

                                    {{-- Tech badge --}}
                                    <div x-show="client.technology">
                                        <span class="kb-badge" x-text="client.technology"></span>
                                    </div>

                                    {{-- Location & email --}}
                                    <div class="kb-card-meta" x-show="client.location">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" x-text="client.location"></span>
                                    </div>
                                    <div class="kb-card-meta" x-show="client.email">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/></svg>
                                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" x-text="client.email"></span>
                                    </div>

                                    {{-- Footer --}}
                                    <div class="kb-card-footer">
                                        <span x-show="client.last_contacted">
                                            Contact: <strong x-text="client.last_contacted" style="color:#42526e;font-weight:600;"></strong>
                                        </span>
                                        <span x-show="!client.last_contacted" style="color:#c1c7d0;">No contact yet</span>

                                        <template x-if="client.is_overdue">
                                            <span class="kb-overdue">
                                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                                Overdue
                                            </span>
                                        </template>
                                        <template x-if="!client.is_overdue && client.next_followup">
                                            <span>Next: <strong x-text="client.next_followup" style="color:#42526e;font-weight:600;"></strong></span>
                                        </template>
                                    </div>

                                </div>
                            </template>

                            {{-- Empty placeholder --}}
                            <div x-show="filtered(status).length===0" class="kb-empty-placeholder"
                                 @click="openCreateModal(status)">
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="margin-right:4px;opacity:0.6;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                <span>Click or drag cards here</span>
                            </div>

                        </div>
                    </div>
                </template>
            </div>{{-- /.kb-board-inner --}}
        </div>{{-- /.kb-board-scroll --}}
    </div>{{-- /.kb-board-wrapper --}}

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- Card Detail Modal                                       --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div x-show="cardModal" x-cloak
         class="kdm-overlay"
         @keydown.escape.window="closeCard()"
         @click.self="closeCard()">

        <template x-if="modalClient">
            <div class="kdm-panel" @click.stop>

                {{-- Header --}}
                <div class="kdm-header">
                    <div class="kdm-avatar" :style="'background:'+avatarBg(modalClient.name)" x-text="modalClient.initials"></div>
                    <span class="kdm-title" x-text="modalClient.name"></span>
                    <span class="kdm-status-pill">
                        <svg width="7" height="7" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                        <span x-text="modalForm.status"></span>
                    </span>
                    <template x-if="modalClient.is_overdue">
                        <span class="kdm-overdue-badge">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Overdue
                        </span>
                    </template>
                    <button class="kdm-close" @click="closeCard()" title="Close">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="kdm-body">

                    {{-- Left: Core editable fields (grouped in cards) --}}
                    <div class="kdm-main">
                        <!-- Section 1: Basic Information -->
                        <div class="kdm-section-card">
                            <div class="kdm-section-header-new">
                                <h3 class="kdm-section-title-new">Basic Information</h3>
                                <p class="kdm-section-desc">Primary contact details for this client.</p>
                            </div>
                            <div class="kdm-section-body">
                                <div class="kdm-field">
                                    <label class="kdm-label">Client Name *</label>
                                    <input type="text" x-model="modalForm.name" class="kdm-input" placeholder="Enter client name">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Email Address</label>
                                    <input type="email" x-model="modalForm.email" class="kdm-input" placeholder="client@example.com">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Mobile No</label>
                                    <input type="text" x-model="modalForm.mobile_no" class="kdm-input" placeholder="+123456789">
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Source & Project Link -->
                        <div class="kdm-section-card">
                            <div class="kdm-section-header-new">
                                <h3 class="kdm-section-title-new">Source & Project</h3>
                                <p class="kdm-section-desc">Origin source and development links.</p>
                            </div>
                            <div class="kdm-section-body">
                                <div class="kdm-field">
                                    <label class="kdm-label">Source URL</label>
                                    <input type="text" x-model="modalForm.source_url" class="kdm-input" placeholder="https://...">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Project Link</label>
                                    <input type="text" x-model="modalForm.project_link" class="kdm-input" placeholder="https://...">
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Social & Communication Links -->
                        <div class="kdm-section-card">
                            <div class="kdm-section-header-new">
                                <h3 class="kdm-section-title-new">Social & Communication</h3>
                                <p class="kdm-section-desc">Links and alternative contact methods.</p>
                            </div>
                            <div class="kdm-section-body">
                                <div class="kdm-field">
                                    <label class="kdm-label">Website</label>
                                    <input type="text" x-model="modalForm.website" class="kdm-input" placeholder="https://...">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">LinkedIn</label>
                                    <input type="text" x-model="modalForm.linkedin" class="kdm-input" placeholder="https://...">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Facebook</label>
                                    <input type="text" x-model="modalForm.facebook" class="kdm-input" placeholder="https://...">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Teams</label>
                                    <input type="text" x-model="modalForm.teams" class="kdm-input" placeholder="ID or link">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">WhatsApp</label>
                                    <input type="text" x-model="modalForm.whatsapp" class="kdm-input" placeholder="+1234...">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Telegram</label>
                                    <input type="text" x-model="modalForm.telegram" class="kdm-input" placeholder="@username">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Instagram</label>
                                    <input type="text" x-model="modalForm.instagram" class="kdm-input" placeholder="https://...">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">YouTube</label>
                                    <input type="text" x-model="modalForm.youtube" class="kdm-input" placeholder="https://...">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">X (Twitter)</label>
                                    <input type="text" x-model="modalForm.x" class="kdm-input" placeholder="https://x.com/...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Status & follow-up + notes (grouped in cards) --}}
                    <div class="kdm-side">
                        <!-- Section 2: Project & Assignment -->
                        <div class="kdm-section-card">
                            <div class="kdm-section-header-new">
                                <h3 class="kdm-section-title-new">Project & Assignment</h3>
                                <p class="kdm-section-desc">Define the tech stack and ownership.</p>
                            </div>
                            <div class="kdm-section-body">
                                <div class="kdm-field">
                                    <label class="kdm-label">Location</label>
                                    <div x-data="{
                                        open: false,
                                        search: '',
                                        get filteredCountries() {
                                            let list = [...allCountries];
                                            if (modalForm.location && !list.includes(modalForm.location)) {
                                                list.unshift(modalForm.location);
                                            }
                                            if (!this.search) return list;
                                            return list.filter(c => c.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        select(country) {
                                            modalForm.location = country;
                                            this.open = false;
                                            this.search = '';
                                        }
                                    }" class="relative">
                                        <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                            class="kdm-input flex items-center justify-between cursor-pointer w-full text-left">
                                            <span x-text="modalForm.location ? modalForm.location : 'Select Country...'"></span>
                                            <svg class="h-3.5 w-3.5 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" x-cloak
                                            class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                            style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                            <div class="mb-1.5 flex-shrink-0">
                                                <input type="text" x-model="search" x-ref="searchInput" placeholder="Search countries..."
                                                    class="kdm-input w-full" style="height: auto;">
                                            </div>
                                            <ul class="overflow-y-auto space-y-0.5 text-xs text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                                <template x-for="country in filteredCountries" :key="country">
                                                    <li @click="select(country)"
                                                        class="cursor-pointer rounded px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                                        :class="modalForm.location === country ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                                        <span x-text="country"></span>
                                                        <svg x-show="modalForm.location === country" class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </li>
                                                </template>
                                                <template x-if="filteredCountries.length === 0">
                                                    <li class="px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 italic">No countries found</li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Technology</label>
                                    <input type="text" x-model="modalForm.technology" class="kdm-input" placeholder="e.g. Laravel, React">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Status</label>
                                    <div x-data="{
                                        open: false,
                                        search: '',
                                        statuses: {{ json_encode($statuses) }},
                                        get filteredStatuses() {
                                            if (!this.search) return this.statuses;
                                            return this.statuses.filter(s => s.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        select(status) {
                                            modalForm.status = status;
                                            this.open = false;
                                            this.search = '';
                                        }
                                    }" class="relative">
                                        <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                            class="kdm-input flex items-center justify-between cursor-pointer w-full text-left">
                                            <span x-text="modalForm.status ? modalForm.status : 'Select Status...'"></span>
                                            <svg class="h-3.5 w-3.5 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" x-cloak
                                            class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                            style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                            <div class="mb-1.5 flex-shrink-0">
                                                <input type="text" x-model="search" x-ref="searchInput" placeholder="Search status..."
                                                    class="kdm-input w-full" style="height: auto;">
                                            </div>
                                            <ul class="overflow-y-auto space-y-0.5 text-xs text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                                <template x-for="status in filteredStatuses" :key="status">
                                                    <li @click="select(status)"
                                                        class="cursor-pointer rounded px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                                        :class="modalForm.status === status ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                                        <span x-text="status"></span>
                                                        <svg x-show="modalForm.status === status" class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </li>
                                                </template>
                                                <template x-if="filteredStatuses.length === 0">
                                                    <li class="px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 italic">No matches</li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @if(auth()->user()->role !== 'employee')
                                <div class="kdm-field">
                                    <label class="kdm-label">Assigned To</label>
                                    <div x-data="{
                                        open: false,
                                        search: '',
                                        get filteredUsers() {
                                            let list = [{id: '', name: '— Unassigned —'}, ...users];
                                            if (!this.search) return list;
                                            return list.filter(u => u.name.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        getUserName() {
                                            let found = users.find(u => u.id == modalForm.assigned_to);
                                            return found ? found.name : '— Unassigned —';
                                        },
                                        select(id) {
                                            modalForm.assigned_to = id;
                                            this.open = false;
                                            this.search = '';
                                        }
                                    }" class="relative">
                                        <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                            class="kdm-input flex items-center justify-between cursor-pointer w-full text-left">
                                            <span x-text="getUserName()"></span>
                                            <svg class="h-3.5 w-3.5 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" x-cloak
                                            class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                            style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                            <div class="mb-1.5 flex-shrink-0">
                                                <input type="text" x-model="search" x-ref="searchInput" placeholder="Search user..."
                                                    class="kdm-input w-full" style="height: auto;">
                                            </div>
                                            <ul class="overflow-y-auto space-y-0.5 text-xs text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                                <template x-for="u in filteredUsers" :key="u.id">
                                                    <li @click="select(u.id)"
                                                        class="cursor-pointer rounded px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                                        :class="modalForm.assigned_to == u.id ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                                        <span x-text="u.name"></span>
                                                        <svg x-show="modalForm.assigned_to == u.id" class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </li>
                                                </template>
                                                <template x-if="filteredUsers.length === 0">
                                                    <li class="px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 italic">No matches</li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Section 4: Follow-up & Notes -->
                        <div class="kdm-section-card">
                            <div class="kdm-section-header-new">
                                <h3 class="kdm-section-title-new">Additional Details</h3>
                                <p class="kdm-section-desc">Scheduling and notes.</p>
                            </div>
                            <div class="kdm-section-body">
                                <div class="kdm-field">
                                    <label class="kdm-label">Last Contacted Date</label>
                                    <input type="text" x-model="modalForm.last_contacted_date" class="kdm-input kdm-date-picker" placeholder="Select date…">
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Follow-up Days</label>
                                    <input type="number" x-model="modalForm.follow_up_days" min="0" class="kdm-input" placeholder="e.g. 7">
                                </div>
                                <div class="kdm-field" x-show="getCalculatedFollowUp()">
                                    <label class="kdm-label">Next Follow-up Date (Calculated)</label>
                                    <div style="font-size:12px;font-weight:600;color:#0079bf;padding:6px 0;" x-text="getCalculatedFollowUp()"></div>
                                </div>
                                <div class="kdm-field">
                                    <label class="kdm-label">Notes</label>
                                    <textarea x-model="modalForm.notes" class="kdm-input kdm-textarea" placeholder="Enter additional notes..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section 6: Communication Templates -->
                        <div class="kdm-section-card">
                            <div class="kdm-section-header-new">
                                <h3 class="kdm-section-title-new">Communication Templates</h3>
                                <p class="kdm-section-desc">Quickly generate copyable templates for this client.</p>
                            </div>
                            <div class="kdm-section-body">
                                <div class="kdm-field">
                                    <label class="kdm-label">Select Template</label>
                                    <div x-data="{
                                        open: false,
                                        search: '',
                                        get filteredTemplates() {
                                            let list = [{id: '', name: '— Select a Template —'}, ...templates];
                                            if (!this.search) return list;
                                            return list.filter(t => t.name.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        getTemplateName() {
                                            let found = templates.find(t => t.id == selectedTemplateId);
                                            return found ? found.name : '— Select a Template —';
                                        },
                                        select(id) {
                                            selectedTemplateId = id;
                                            this.open = false;
                                            this.search = '';
                                        }
                                    }" class="relative">
                                        <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                            class="kdm-input flex items-center justify-between cursor-pointer w-full text-left">
                                            <span x-text="getTemplateName()"></span>
                                            <svg class="h-3.5 w-3.5 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" x-cloak
                                            class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                            style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                            <div class="mb-1.5 flex-shrink-0">
                                                <input type="text" x-model="search" x-ref="searchInput" placeholder="Search template..."
                                                    class="kdm-input w-full" style="height: auto;">
                                            </div>
                                            <ul class="overflow-y-auto space-y-0.5 text-xs text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                                <template x-for="t in filteredTemplates" :key="t.id">
                                                    <li @click="select(t.id)"
                                                        class="cursor-pointer rounded px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                                        :class="selectedTemplateId == t.id ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                                        <span x-text="t.name"></span>
                                                        <svg x-show="selectedTemplateId == t.id" class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </li>
                                                </template>
                                                <template x-if="filteredTemplates.length === 0">
                                                    <li class="px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 italic">No matches</li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <template x-if="getRenderedTemplate()">
                                    <div style="margin-top:12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px;" class="dark:bg-white/[0.02] dark:border-gray-800">
                                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                            <span style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9ca3af;" x-text="getRenderedTemplate().type + ' Preview'"></span>
                                            <div style="display:flex;gap:4px;">
                                                <button type="button" @click="copyToClipboard(getRenderedTemplate().content)" style="background:#e0f2fe;color:#0369a1;border:none;border-radius:4px;padding:3px 8px;font-size:10px;font-weight:700;cursor:pointer;" class="dark:bg-brand-500/10 dark:color-brand-400">Copy Body</button>
                                                <template x-if="getRenderedTemplate().subject">
                                                    <button type="button" @click="copyToClipboard(getRenderedTemplate().subject)" style="background:#f0fdf4;color:#166534;border:none;border-radius:4px;padding:3px 8px;font-size:10px;font-weight:700;cursor:pointer;" class="dark:bg-green-500/10 dark:color-green-400">Copy Subject</button>
                                                </template>
                                            </div>
                                        </div>
                                        <template x-if="getRenderedTemplate().subject">
                                            <div style="margin-bottom:8px;font-size:12px;">
                                                <strong style="color:#4b5563;" class="dark:text-gray-400">Subject:</strong>
                                                <span style="color:#1f2937;font-weight:600;" class="dark:text-white" x-text="getRenderedTemplate().subject"></span>
                                            </div>
                                        </template>
                                        <pre style="margin:0;font-family:monospace;font-size:11px;white-space:pre-wrap;color:#374151;max-height:200px;overflow-y:auto;" class="dark:text-gray-300" x-text="getRenderedTemplate().content"></pre>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="kdm-footer">
                    <a :href="modalClient.show_url" class="kdm-btn-view" target="_blank">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        Full Details
                    </a>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <button class="kdm-close" @click="closeCard()" style="width:auto;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:600;">Cancel</button>
                        <button class="kdm-btn-save" @click="saveCard()" :disabled="modalSaving">
                            <template x-if="modalSaving">
                                <span class="kdm-saving-spinner"></span>
                            </template>
                            <template x-if="!modalSaving">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </template>
                            <span x-text="modalSaving ? 'Saving…' : 'Save Changes'"></span>
                        </button>
                    </div>
                </div>

            </div>
        </template>
    </div>

    {{-- Create Client Modal --}}
    <div x-show="createModal" x-cloak
         class="kdm-overlay"
         @keydown.escape.window="closeCreateModal()"
         @click.self="closeCreateModal()">

        <div class="kdm-panel" @click.stop>

            {{-- Header --}}
            <div class="kdm-header">
                <div class="kdm-avatar" style="background:#0079bf;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">+</div>
                <span class="kdm-title">Add New Client</span>
                <button class="kdm-close" @click="closeCreateModal()" title="Close">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="kdm-body">

                {{-- Left Column (grouped in cards) --}}
                <div class="kdm-main">
                    <!-- Section 1: Basic Information -->
                    <div class="kdm-section-card">
                        <div class="kdm-section-header-new">
                            <h3 class="kdm-section-title-new">Basic Information</h3>
                            <p class="kdm-section-desc">Primary contact details for this client.</p>
                        </div>
                        <div class="kdm-section-body">
                            <div class="kdm-field">
                                <label class="kdm-label">Client Name *</label>
                                <input type="text" x-model="createForm.name" class="kdm-input" placeholder="Enter client name">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Email Address</label>
                                <input type="email" x-model="createForm.email" class="kdm-input" placeholder="client@example.com">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Mobile No</label>
                                <input type="text" x-model="createForm.mobile_no" class="kdm-input" placeholder="+123456789">
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Source & Project Link -->
                    <div class="kdm-section-card">
                        <div class="kdm-section-header-new">
                            <h3 class="kdm-section-title-new">Source & Project</h3>
                            <p class="kdm-section-desc">Origin source and development links.</p>
                        </div>
                        <div class="kdm-section-body">
                            <div class="kdm-field">
                                <label class="kdm-label">Source URL</label>
                                <input type="text" x-model="createForm.source_url" class="kdm-input" placeholder="https://...">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Project Link</label>
                                <input type="text" x-model="createForm.project_link" class="kdm-input" placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Social & Communication Links -->
                    <div class="kdm-section-card">
                        <div class="kdm-section-header-new">
                            <h3 class="kdm-section-title-new">Social & Communication</h3>
                            <p class="kdm-section-desc">Links and alternative contact methods.</p>
                        </div>
                        <div class="kdm-section-body">
                            <div class="kdm-field">
                                <label class="kdm-label">Website</label>
                                <input type="text" x-model="createForm.website" class="kdm-input" placeholder="https://...">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">LinkedIn</label>
                                <input type="text" x-model="createForm.linkedin" class="kdm-input" placeholder="https://...">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Facebook</label>
                                <input type="text" x-model="createForm.facebook" class="kdm-input" placeholder="https://...">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Teams</label>
                                <input type="text" x-model="createForm.teams" class="kdm-input" placeholder="ID or link">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">WhatsApp</label>
                                <input type="text" x-model="createForm.whatsapp" class="kdm-input" placeholder="+1234...">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Telegram</label>
                                <input type="text" x-model="createForm.telegram" class="kdm-input" placeholder="@username">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Instagram</label>
                                <input type="text" x-model="createForm.instagram" class="kdm-input" placeholder="https://...">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">YouTube</label>
                                <input type="text" x-model="createForm.youtube" class="kdm-input" placeholder="https://...">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">X (Twitter)</label>
                                <input type="text" x-model="createForm.x" class="kdm-input" placeholder="https://x.com/...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column (grouped in cards) --}}
                <div class="kdm-side">
                    <!-- Section 2: Project & Assignment -->
                    <div class="kdm-section-card">
                        <div class="kdm-section-header-new">
                            <h3 class="kdm-section-title-new">Project & Assignment</h3>
                            <p class="kdm-section-desc">Define the tech stack and ownership.</p>
                        </div>
                        <div class="kdm-section-body">
                            <div class="kdm-field">
                                <label class="kdm-label">Location</label>
                                <div x-data="{
                                    open: false,
                                    search: '',
                                    get filteredCountries() {
                                        if (!this.search) return allCountries;
                                        return allCountries.filter(c => c.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(country) {
                                        createForm.location = country;
                                        this.open = false;
                                        this.search = '';
                                    }
                                }" class="relative">
                                    <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                        class="kdm-input flex items-center justify-between cursor-pointer w-full text-left">
                                        <span x-text="createForm.location ? createForm.location : 'Select Country...'"></span>
                                        <svg class="h-3.5 w-3.5 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-cloak
                                        class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                        style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                        <div class="mb-1.5 flex-shrink-0">
                                            <input type="text" x-model="search" x-ref="searchInput" placeholder="Search countries..."
                                                class="kdm-input w-full" style="height: auto;">
                                        </div>
                                        <ul class="overflow-y-auto space-y-0.5 text-xs text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                            <template x-for="country in filteredCountries" :key="country">
                                                <li @click="select(country)"
                                                    class="cursor-pointer rounded px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                                    :class="createForm.location === country ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                                    <span x-text="country"></span>
                                                    <svg x-show="createForm.location === country" class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </li>
                                            </template>
                                            <template x-if="filteredCountries.length === 0">
                                                <li class="px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 italic">No countries found</li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Technology</label>
                                <input type="text" x-model="createForm.technology" class="kdm-input" placeholder="e.g. Laravel, React">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Status</label>
                                <div x-data="{
                                    open: false,
                                    search: '',
                                    statuses: {{ json_encode($statuses) }},
                                    get filteredStatuses() {
                                        if (!this.search) return this.statuses;
                                        return this.statuses.filter(s => s.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(status) {
                                        createForm.status = status;
                                        this.open = false;
                                        this.search = '';
                                    }
                                }" class="relative">
                                    <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                        class="kdm-input flex items-center justify-between cursor-pointer w-full text-left">
                                        <span x-text="createForm.status ? createForm.status : 'Select Status...'"></span>
                                        <svg class="h-3.5 w-3.5 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-cloak
                                        class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                        style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                        <div class="mb-1.5 flex-shrink-0">
                                            <input type="text" x-model="search" x-ref="searchInput" placeholder="Search status..."
                                                class="kdm-input w-full" style="height: auto;">
                                        </div>
                                        <ul class="overflow-y-auto space-y-0.5 text-xs text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                            <template x-for="status in filteredStatuses" :key="status">
                                                <li @click="select(status)"
                                                    class="cursor-pointer rounded px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                                    :class="createForm.status === status ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                                    <span x-text="status"></span>
                                                    <svg x-show="createForm.status === status" class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </li>
                                            </template>
                                            <template x-if="filteredStatuses.length === 0">
                                                <li class="px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 italic">No matches</li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @if(auth()->user()->role !== 'employee')
                            <div class="kdm-field">
                                <label class="kdm-label">Assigned To</label>
                                <div x-data="{
                                    open: false,
                                    search: '',
                                    get filteredUsers() {
                                        let list = [{id: '', name: '— Unassigned —'}, ...users];
                                        if (!this.search) return list;
                                        return list.filter(u => u.name.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    getUserName() {
                                        let found = users.find(u => u.id == createForm.assigned_to);
                                        return found ? found.name : '— Unassigned —';
                                    },
                                    select(id) {
                                        createForm.assigned_to = id;
                                        this.open = false;
                                        this.search = '';
                                    }
                                }" class="relative">
                                    <button type="button" @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()) }"
                                        class="kdm-input flex items-center justify-between cursor-pointer w-full text-left">
                                        <span x-text="getUserName()"></span>
                                        <svg class="h-3.5 w-3.5 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-cloak
                                        class="absolute left-0 z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-955"
                                        style="max-height: 250px; overflow: hidden; display: flex; flex-direction: column;">
                                        <div class="mb-1.5 flex-shrink-0">
                                            <input type="text" x-model="search" x-ref="searchInput" placeholder="Search user..."
                                                class="kdm-input w-full" style="height: auto;">
                                        </div>
                                        <ul class="overflow-y-auto space-y-0.5 text-xs text-gray-700 dark:text-gray-300 flex-1 kb-scroll">
                                            <template x-for="u in filteredUsers" :key="u.id">
                                                <li @click="select(u.id)"
                                                    class="cursor-pointer rounded px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white flex items-center justify-between"
                                                    :class="createForm.assigned_to == u.id ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-medium' : ''">
                                                    <span x-text="u.name"></span>
                                                    <svg x-show="createForm.assigned_to == u.id" class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </li>
                                            </template>
                                            <template x-if="filteredUsers.length === 0">
                                                <li class="px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 italic">No matches</li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Section 4: Follow-up & Notes -->
                    <div class="kdm-section-card">
                        <div class="kdm-section-header-new">
                            <h3 class="kdm-section-title-new">Additional Details</h3>
                            <p class="kdm-section-desc">Scheduling and notes.</p>
                        </div>
                        <div class="kdm-section-body">
                            <div class="kdm-field">
                                <label class="kdm-label">Last Contacted Date</label>
                                <input type="text" x-model="createForm.last_contacted_date" class="kdm-input kdm-create-date-picker" placeholder="Select date…">
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Follow-up Days</label>
                                <input type="number" x-model="createForm.follow_up_days" min="0" class="kdm-input" placeholder="e.g. 7">
                            </div>
                            <div class="kdm-field" x-show="getCalculatedCreateFollowUp()">
                                <label class="kdm-label">Next Follow-up Date (Calculated)</label>
                                <div style="font-size:12px;font-weight:600;color:#0079bf;padding:6px 0;" x-text="getCalculatedCreateFollowUp()"></div>
                            </div>
                            <div class="kdm-field">
                                <label class="kdm-label">Notes</label>
                                <textarea x-model="createForm.notes" class="kdm-input kdm-textarea" placeholder="Enter additional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="kdm-footer">
                <button class="kdm-btn-cancel" @click="closeCreateModal()">Cancel</button>
                <button class="kdm-btn-save" @click="saveNewClient()" :disabled="createSaving">
                    <template x-if="createSaving">
                        <span class="kdm-saving-spinner"></span>
                    </template>
                    <template x-if="!createSaving">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </template>
                    <span x-text="createSaving ? 'Saving…' : 'Save Client'"></span>
                </button>
            </div>

        </div>
    </div>

</div>

<script>
(function(){
    var scroll = document.getElementById('kb-scroll');
    var btnL   = document.getElementById('kb-left');
    var btnR   = document.getElementById('kb-right');
    if(!scroll) return;
    function updateBtns(){
        btnL.style.opacity = scroll.scrollLeft > 8 ? '1' : '0';
        btnL.style.pointerEvents = scroll.scrollLeft > 8 ? 'auto' : 'none';
        var atEnd = scroll.scrollLeft + scroll.clientWidth >= scroll.scrollWidth - 8;
        btnR.style.opacity = atEnd ? '0' : '1';
        btnR.style.pointerEvents = atEnd ? 'none' : 'auto';
    }
    scroll.addEventListener('scroll', updateBtns);
    window.addEventListener('resize', updateBtns);
    // initial state after Alpine renders
    setTimeout(updateBtns, 300);
})();
</script>

<form id="del-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection
