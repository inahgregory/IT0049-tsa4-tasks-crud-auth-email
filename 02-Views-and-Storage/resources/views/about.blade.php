@extends('layouts.app')
@section('title', 'About')
@section('content')
<section class="page-intro compact"><div><p class="eyebrow">SMALL PLANS, STEADY PROGRESS</p><h1>A clearer kind of day<span>.</span></h1><p class="lead">Tasks for Today keeps daily priorities simple and the bigger picture close.</p></div></section>
<section class="about-grid">
    <article class="panel details"><p class="eyebrow">ABOUT THE APPLICATION</p><h2>Built around your day</h2><p>Start with the Welcome page to see only the tasks scheduled for today. Open the Task List for every task in date order, or visit Profile to view the workspace owner.</p><p>This application was developed for IT0049 Technical Summative Assessment 4. It rebuilds the Tasks for Today Management System in Laravel using migrations, Eloquent, validated task forms, archiving, Breeze authentication, and automatic completion emails.</p><a class="text-link" href="{{ route('welcome') }}">Back to today's tasks &rarr;</a></article>
    <article class="panel details"><p class="eyebrow">MEET THE DEVELOPER</p><h2>Inah Angela Gregorio</h2><dl><div><dt>Section</dt><dd>BSITCST - AC32</dd></div><div><dt>Course</dt><dd>IT0049 &middot; Web System Technologies</dd></div><div><dt>Assessment</dt><dd>TSA4 &middot; Tasks for Today Management System</dd></div><div><dt>Built with</dt><dd>Laravel, PHP, Eloquent, Blade, CSS</dd></div></dl></article>
</section>
@endsection

