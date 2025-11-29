<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Aktivitas </title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            overflow-x: hidden;
        }

        html {
            overflow-x: hidden;
        }

        .main-container {
            display: flex;
            min-height: 100vh;
        }

        /* Hamburger Menu Button */
        .hamburger-menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 8px;
            background: none;
            border: none;
            z-index: 1001;
        }

        .hamburger-menu span {
            width: 25px;
            height: 3px;
            background-color: #333;
            transition: all 0.3s;
            border-radius: 2px;
        }

        .hamburger-menu:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 6px;
        }

        .hamburger-menu:active {
            background-color: rgba(0, 0, 0, 0.1);
        }

        .hamburger-menu.active span:nth-child(1) {
            transform: rotate(45deg) translate(7px, 7px);
        }

        .hamburger-menu.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger-menu.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }

        /* Sidebar Overlay for Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0066CC 0%, #003D82 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: transform 0.3s ease;
            -webkit-overflow-scrolling: touch;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        /* Show sidebar toggle button */
        .sidebar-toggle-hint {
            position: fixed;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
            color: white;
            padding: 10px 6px;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s, left 0.3s;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar.hidden ~ .main-content .sidebar-toggle-hint {
            opacity: 0.7;
            pointer-events: auto;
        }

        .sidebar-toggle-hint:hover {
            opacity: 1 !important;
            left: 0;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            width: calc(100vw - 260px);
            overflow-x: auto;
        }

        .main-content.expanded {
            margin-left: 0;
            width: 100vw;
        }

        /* Scrollbar for main-content */
        .main-content::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        .main-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 5px;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
            border-radius: 5px;
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #0052A3 0%, #002D5C 100%);
        }

        /* Scrollbar styling for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            font-size: 20px;
            font-weight: 600;
        }

        .sidebar-header p {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 4px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 15px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        .menu-item:hover,
        .menu-item:active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .menu-item.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 4px solid white;
        }

        .menu-item svg {
            width: 20px;
            height: 20px;
            margin-right: 12px;
        }

        /* Dropdown Menu */
        .menu-group {
            position: relative;
        }

        .menu-item-dropdown {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 15px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        .menu-item-dropdown:hover,
        .menu-item-dropdown:active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .menu-item-dropdown svg {
            width: 20px;
            height: 20px;
            margin-right: 12px;
        }

        .dropdown-toggle {
            width: 16px;
            height: 16px;
            transition: transform 0.3s;
        }

        .menu-item-dropdown.active-dropdown .dropdown-toggle {
            transform: rotate(180deg);
        }

        .submenu {
            display: none;
            background-color: rgba(0, 0, 0, 0.1);
            padding: 0;
            border-left: 3px solid rgba(255, 255, 255, 0.2);
        }

        .submenu.show {
            display: block;
        }

        .submenu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px 12px 40px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 14px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        .submenu-item:hover,
        .submenu-item:active {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .submenu-item.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Nested dropdown styles */
        .submenu-item-dropdown {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px 12px 40px;
            color: white;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        .submenu-item-dropdown:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .dropdown-toggle-sub {
            transition: transform 0.3s;
        }

        .submenu-item-dropdown.active-sub .dropdown-toggle-sub {
            transform: rotate(180deg);
        }

        .sub-submenu {
            display: none;
            background-color: rgba(0, 0, 0, 0.15);
            padding: 0;
            border-left: 3px solid rgba(255, 255, 255, 0.3);
        }

        .sub-submenu.show {
            display: block;
        }

        .sub-submenu-item {
            display: flex;
            align-items: center;
            padding: 10px 20px 10px 60px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 13px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        .sub-submenu-item:hover,
        .sub-submenu-item:active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sub-submenu-item.active {
            background-color: rgba(255, 255, 255, 0.25);
        }

        /* Navbar */
        .navbar {
            background: white;
            padding: 16px 32px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: sticky;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .navbar-left {
            min-width: max-content;
        }

        .navbar-left h1 {
            font-size: 24px;
            color: #333;
            white-space: nowrap;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
            min-width: max-content;
        }

        /* Notification Styles */
        .notification-container {
            position: relative;
        }

        .notification-bell {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-bell:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .notification-bell svg {
            color: #333;
        }

        .notification-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            background: #f44336;
            color: white;
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: 600;
            min-width: 18px;
            text-align: center;
        }

        .notification-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 360px;
            max-height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            display: none;
            z-index: 1000;
            overflow: hidden;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e0e0e0;
            background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        }

        .notification-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: white;
        }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.warning {
            border-left: 4px solid #ff9800;
        }

        .notification-item.info {
            border-left: 4px solid #2196F3;
        }

        .notification-item.success {
            border-left: 4px solid #4CAF50;
        }

        .notification-title {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 4px;
        }

        .notification-message {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .notification-link {
            display: inline-block;
            font-size: 13px;
            color: #0066CC;
            text-decoration: none;
            font-weight: 500;
        }

        .notification-link:hover {
            text-decoration: underline;
        }

        .notification-empty {
            padding: 40px 20px;
            text-align: center;
            color: #999;
            font-size: 14px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .user-email {
            font-size: 12px;
            color: #666;
        }

        .btn-logout {
            padding: 8px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-logout:hover {
            background-color: #d32f2f;
        }

        /* Content Area */
        .content {
            padding: 32px;
            min-width: max-content;
        }

        /* Scrollbar for content */
        .content::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        .content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 5px;
        }

        .content::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
            border-radius: 5px;
        }

        .content::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #0052A3 0%, #002D5C 100%);
        }

        .page-header {
            margin-bottom: 24px;
            min-width: max-content;
        }

        .page-header h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 8px;
            white-space: nowrap;
        }

        .page-header p {
            color: #666;
            font-size: 14px;
            white-space: nowrap;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            overflow: visible;
        }

        .card h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 16px;
        }

        /* Pagination Fix */
        .pagination-wrapper nav {
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper nav svg {
            width: 16px !important;
            height: 16px !important;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .sidebar {
                width: 240px;
            }

            .main-content {
                margin-left: 240px;
            }

            .menu-item,
            .menu-item-dropdown {
                padding: 12px 16px;
                font-size: 14px;
            }

            .submenu-item {
                padding: 10px 16px 10px 36px;
                font-size: 13px;
            }
        }

        @media (max-width: 1024px) {
            .navbar-right .user-details {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar.hidden {
                transform: translateX(-100%);
            }

            .sidebar:not(.hidden) {
                transform: translateX(0);
            }

            .navbar {
                padding: 12px 16px;
            }

            .navbar-left h1 {
                font-size: 18px;
            }

            .navbar-right {
                gap: 10px;
            }

            .navbar-right .user-info {
                gap: 8px;
            }

            .navbar-right .user-avatar {
                width: 35px;
                height: 35px;
            }

            .btn-logout {
                padding: 6px 12px;
                font-size: 12px;
            }

            .content {
                padding: 16px;
            }

            .main-content {
                width: 100vw;
                overflow-x: visible;
            }

            .content {
                min-width: auto;
            }

            .page-header h2 {
                font-size: 20px;
            }

            .card {
                padding: 16px;
                overflow: visible;
            }
        }

        /* Tablet Landscape */
        @media (min-width: 769px) and (max-width: 1024px) and (orientation: landscape) {
            .sidebar {
                width: 220px;
            }

            .main-content {
                margin-left: 220px;
                width: calc(100vw - 220px);
            }

            .menu-item,
            .menu-item-dropdown {
                padding: 11px 15px;
                font-size: 14px;
            }

            .submenu-item {
                padding: 10px 15px 10px 32px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .navbar-left h1 {
                font-size: 16px;
            }

            .navbar-right .user-avatar {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            .btn-logout {
                padding: 5px 10px;
                font-size: 11px;
            }

            .content {
                padding: 12px;
            }

            .page-header h2 {
                font-size: 18px;
            }

            .card {
                padding: 12px;
            }

            .table-container {
                font-size: 12px;
            }

            .table th,
            .table td {
                padding: 8px 10px;
            }

            .notification-dropdown {
                width: 300px;
                right: -20px;
            }

            .notification-bell {
                padding: 6px;
            }

            .notification-bell svg {
                width: 20px;
                height: 20px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>Pipeline</h2>
                <p>Management System</p>
            </div>
            
            <nav class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                
                <a href="{{ route('aktivitas.index') }}" class="menu-item {{ request()->routeIs('aktivitas.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Aktivitas
                </a>
                
                <a href="{{ route('pipeline.index') }}" class="menu-item {{ request()->routeIs('pipeline.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Pipeline
                </a>
                
                @if(auth()->user()->isAdmin())
                <a href="{{ route('rekap.index') }}" class="menu-item {{ request()->routeIs('rekap.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Validasi
                </a>
                @endif
                
                {{-- @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                <a href="{{ route('nasabah.index') }}" class="menu-item {{ request()->routeIs('nasabah.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Nasabah
                </a>
                @endif --}}
                
                @if(auth()->user()->isAdmin())
                <a href="{{ route('uker.index') }}" class="menu-item {{ request()->routeIs('uker.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Uker
                </a>
                @endif
                
                @if(auth()->user()->isAdmin())
                <a href="{{ route('rmft.index') }}" class="menu-item {{ request()->routeIs('rmft.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    RMFT
                </a>
                
                <a href="{{ route('akun.index') }}" class="menu-item {{ request()->routeIs('akun.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Akun
                </a>
                
                <a href="{{ route('rencana-aktivitas.index') }}" class="menu-item {{ request()->routeIs('rencana-aktivitas.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Rencana Aktivitas
                </a>
                @endif
                
                @if(auth()->user()->isManager() || auth()->user()->isRMFT())
                <!-- Pull Of Pipeline Menu for Manager & RMFT (Read-only) -->
                <div class="menu-group">
                    <div class="menu-item-dropdown" onclick="toggleDropdown(this)">
                        <span style="display: flex; align-items: center;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            Pull Of Pipeline
                        </span>
                        <svg class="dropdown-toggle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    <div class="submenu">
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 1 - Optimalisasi Digital Channel
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('manager-pull-pipeline.merchant-savol') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.merchant-savol') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Merchant Savol
                            </a>
                            <a href="{{ route('manager-pull-pipeline.penurunan-merchant') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.penurunan-merchant') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Penurunan Merchant
                            </a>
                            <a href="{{ route('manager-pull-pipeline.penurunan-casa-brilink') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.penurunan-casa-brilink') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Penurunan Casa Brilink
                            </a>
                            <a href="{{ route('manager-pull-pipeline.brilink-saldo-kurang') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.brilink-saldo-kurang') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Brilink Saldo < 10 Juta
                            </a>
                            <a href="{{ route('manager-pull-pipeline.qlola-non-debitur') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.qlola-non-debitur') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Qlola Non Debitur
                            </a>
                            <a href="{{ route('manager-pull-pipeline.non-debitur-vol-besar') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.non-debitur-vol-besar') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Non Debitur Vol Besar
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 2 - Rekening Debitur Transaksi
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('manager-pull-pipeline.qlola-nonaktif') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.qlola-nonaktif') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Qlola (Belum ada Qlola / ada namun nonaktif)
                            </a>
                            <a href="{{ route('manager-pull-pipeline.user-aktif-casa-kecil') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.user-aktif-casa-kecil') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                User Aktif Casa Kecil
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 3 - Optimalisasi Business Cluster
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('manager-pull-pipeline.optimalisasi-business-cluster') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.optimalisasi-business-cluster') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Optimalisasi Business Cluster
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 4 - Peningkatan Payroll Berkualitas
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('manager-pull-pipeline.existing-payroll') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.existing-payroll') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Existing Payroll
                            </a>
                            <a href="{{ route('manager-pull-pipeline.potensi-payroll') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.potensi-payroll') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Potensi Payroll
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 6 - Kolaborasi Perusahaan Anak
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('manager-pull-pipeline.perusahaan-anak') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.perusahaan-anak') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Perusahaan Anak
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 7 - Optimalisasi Nasabah Prioritas & BOC BOD Nasabah Wholesale & Komersial
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('manager-pull-pipeline.penurunan-prioritas-ritel-mikro') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.penurunan-prioritas-ritel-mikro') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Penurunan Prioritas Ritel & Mikro
                            </a>
                            <a href="{{ route('manager-pull-pipeline.aum-dpk') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.aum-dpk') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                AUM>2M DPK<50 juta
                            </a>
                            <a href="{{ route('manager-pull-pipeline.nasabah-downgrade') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.nasabah-downgrade') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                </svg>
                                Nasabah Downgrade
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 8 - Penguatan Produk & Fungsi RM
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('manager-pull-pipeline.strategi8') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.strategi8') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Winback
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                                </svg>
                                Layering
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('manager-pull-pipeline.layering') }}" class="sub-submenu-item {{ request()->routeIs('manager-pull-pipeline.layering') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                Winback
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                @if(auth()->user()->isAdmin())
                <!-- Pull Of Pipeline Menu for Admin -->
                <div class="menu-group">
                    <div class="menu-item-dropdown" onclick="toggleDropdown(this)">
                        <span style="display: flex; align-items: center;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            Pull Of Pipeline
                        </span>
                        <svg class="dropdown-toggle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    <div class="submenu">
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 1 - Optimalisasi Digital Channel
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('merchant-savol.index') }}" class="sub-submenu-item {{ request()->routeIs('merchant-savol.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)
                            </a>
                            <a href="{{ route('penurunan-merchant.index') }}" class="sub-submenu-item {{ request()->routeIs('penurunan-merchant.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                PENURUNAN CASA MERCHANT (QRIS & EDC)
                            </a>
                            <a href="{{ route('penurunan-casa-brilink.index') }}" class="sub-submenu-item {{ request()->routeIs('penurunan-casa-brilink.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                PENURUNAN CASA BRILINK
                            </a>
                            <a href="{{ route('brilink.index') }}" class="sub-submenu-item {{ request()->routeIs('brilink.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                BRILINK SALDO < 10 JUTA
                            </a>
                            <a href="{{ route('qlola-non-debitur.index') }}" class="sub-submenu-item {{ request()->routeIs('qlola-non-debitur.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Qlola Non Debitur
                            </a>
                            <a href="{{ route('non-debitur-vol-besar.index') }}" class="sub-submenu-item {{ request()->routeIs('non-debitur-vol-besar.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Non Dbitur Vol Besar CASA Kecil
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 2 - Rekening Debitur Transaksi
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('qlola-nonaktif.index') }}" class="sub-submenu-item {{ request()->routeIs('qlola-nonaktif.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Qlola (Belum ada Qlola / ada namun nonaktif)
                            </a>
                            <a href="{{ route('user-aktif-casa-kecil.index') }}" class="sub-submenu-item {{ request()->routeIs('user-aktif-casa-kecil.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                User Aktif Casa Kecil
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 3 - Optimalisasi Business Cluster
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('optimalisasi-business-cluster.index') }}" class="sub-submenu-item {{ request()->routeIs('optimalisasi-business-cluster.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Optimalisasi Business Cluster
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 4 - Peningkatan Payroll Berkualitas
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('existing-payroll.index') }}" class="sub-submenu-item {{ request()->routeIs('existing-payroll.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Existing Payroll
                            </a>
                            <a href="{{ route('potensi-payroll.index') }}" class="sub-submenu-item {{ request()->routeIs('potensi-payroll.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Potensi Payroll
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 6 - Kolaborasi Perusahaan Anak
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('perusahaan-anak.index') }}" class="sub-submenu-item {{ request()->routeIs('perusahaan-anak.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                List Perusahaan Anak
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 7 - Optimalisasi Nasabah Prioritas & BOC BOD Nasabah Wholesale & Komersial
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('penurunan-prioritas-ritel-mikro.index') }}" class="sub-submenu-item {{ request()->routeIs('penurunan-prioritas-ritel-mikro.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Penurunan Prioritas Ritel & Mikro
                            </a>
                            <a href="{{ route('aum-dpk.index') }}" class="sub-submenu-item {{ request()->routeIs('aum-dpk.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                AUM>2M DPK<50 juta
                            </a>
                            <a href="{{ route('nasabah-downgrade.index') }}" class="sub-submenu-item {{ request()->routeIs('nasabah-downgrade.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                </svg>
                                Nasabah Downgrade
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Strategi 8 - Penguatan Produk & Fungsi RM
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('strategi8.index') }}" class="sub-submenu-item {{ request()->routeIs('strategi8.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Winback Penguatan Produk & Fungsi RM
                            </a>
                        </div>
                        <div class="submenu-item-dropdown" onclick="toggleSubDropdown(this)">
                            <span style="display: flex; align-items: center; width: 100%;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                                </svg>
                                Layering
                            </span>
                            <svg class="dropdown-toggle-sub" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="sub-submenu">
                            <a href="{{ route('layering.index') }}" class="sub-submenu-item {{ request()->routeIs('layering.*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                Winback
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Profile Menu - All users -->
                <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <a href="{{ route('profile.index') }}" class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Profil Saya
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Navbar -->
            <nav class="navbar">
                <div class="navbar-left" style="display: flex; align-items: center; gap: 15px;">
                    <button class="hamburger-menu" id="hamburgerMenu" onclick="toggleSidebar()" title="Toggle Sidebar">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="navbar-right">
                    <!-- Notification Bell -->
                    <div class="notification-container">
                        <button class="notification-bell" id="notificationBell" onclick="toggleNotifications()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                        </button>
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-header">
                                <h3>Notifikasi</h3>
                            </div>
                            <div class="notification-list" id="notificationList">
                                <div class="notification-empty">Tidak ada notifikasi</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="user-info">
                        <div class="user-avatar">
                            @if(Auth::user()->photo)
                                <img src="{{ asset('storage/photos/' . Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <div class="user-details">
                            <span class="user-name">{{ Auth::user()->name ?? 'User' }}</span>
                            <span class="user-email">{{ Auth::user()->email ?? '' }}</span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-logout">Logout</button>
                    </form>
                </div>
            </nav>

            <!-- Content -->
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleDropdown(element) {
            const submenu = element.nextElementSibling;
            submenu.classList.toggle('show');
            element.classList.toggle('active-dropdown');
        }

        function toggleSubDropdown(element) {
            const subSubmenu = element.nextElementSibling;
            subSubmenu.classList.toggle('show');
            element.classList.toggle('active-sub');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            const overlay = document.getElementById('sidebarOverlay');
            const hamburger = document.getElementById('hamburgerMenu');
            
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');
            hamburger.classList.toggle('active');
            
            // Only show overlay on mobile
            if (window.innerWidth <= 768) {
                overlay.classList.toggle('active');
            }
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const hamburger = document.getElementById('hamburgerMenu');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !hamburger.contains(event.target) && !sidebar.classList.contains('hidden')) {
                    toggleSidebar();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const overlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth > 768) {
                overlay.classList.remove('active');
            }
        });

        // Initialize: Hide sidebar on mobile by default
        window.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.add('hidden');
                mainContent.classList.add('expanded');
            }
            
            // Load notifications
            loadNotifications();
        });

        // Notification functions
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const notificationContainer = document.querySelector('.notification-container');
            const notificationBell = document.getElementById('notificationBell');
            
            if (notificationContainer && !notificationContainer.contains(event.target)) {
                const dropdown = document.getElementById('notificationDropdown');
                dropdown.classList.remove('show');
            }
        });

        function loadNotifications() {
            // Load notification count
            fetch('{{ route("api.notifications.count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error loading notification count:', error));

            // Load notifications
            fetch('{{ route("api.notifications") }}')
                .then(response => response.json())
                .then(data => {
                    const notificationList = document.getElementById('notificationList');
                    
                    if (data.notifications && data.notifications.length > 0) {
                        notificationList.innerHTML = '';
                        data.notifications.forEach(notification => {
                            const item = document.createElement('div');
                            item.className = `notification-item ${notification.type}`;
                            item.innerHTML = `
                                <div class="notification-title">${notification.title}</div>
                                <div class="notification-message">${notification.message}</div>
                                <a href="${notification.link}" class="notification-link">${notification.link_text}</a>
                            `;
                            notificationList.appendChild(item);
                        });
                    } else {
                        notificationList.innerHTML = '<div class="notification-empty">Tidak ada notifikasi</div>';
                    }
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        // Reload notifications every 5 minutes
        setInterval(loadNotifications, 300000);
    </script>

    @stack('scripts')
</body>
</html>




