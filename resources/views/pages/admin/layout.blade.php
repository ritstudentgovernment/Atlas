@extends('layouts.main')

@section('title', 'Admin Panel')

@section('page_head')

    <link rel="stylesheet" href="{{ mix('/css/admin.css') }}">

@endsection

@section('body')

    <el-container style="border: 1px solid #eee; height: calc(100vh - 101px);">
        <el-aside width="auto">
            <admin-nav
                    default-activated="@yield('nav-active-index')"
                    default-opened="@yield('nav-open-index')">
            </admin-nav>
        </el-aside>
        <el-container>
            <el-header style="text-align: right; font-size: 12px">
                <h1 class="uk-heading-divider"><span>@yield('page-title')</span></h1>
            </el-header>
            <el-main>
                @yield('page-content')
            </el-main>
        </el-container>
    </el-container>

@endsection

@section('scripts')

    <script src="{{ mix('/js/admin.js') }}" async defer></script>

@endsection