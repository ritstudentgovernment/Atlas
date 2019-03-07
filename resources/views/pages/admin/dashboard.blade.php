@extends('pages.admin.layout')

@section('nav-active-index', "0")
@section('page-title', 'Admin Dashboard')

@section('page-content')

    <el-row :gutter="20">
        <el-col :span="8"><div class="grid-content bg-purple"></div></el-col>
        <el-col :span="16"><div class="grid-content bg-purple"></div></el-col>
    </el-row>

@endsection