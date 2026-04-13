@extends('layouts.app', ['title' => 'DOST PES Portal'])

@php
    $materialSearchTerm = strtolower($materialSearch);
    $filteredMaterials = $materials->filter(fn ($item) => ($materialSearchTerm === '' || str_contains(strtolower($item->title.' '.$item->type.' '.$item->division), $materialSearchTerm)) && ($materialTypeFilter === '' || $materialTypeFilter === 'All' || $item->type === $materialTypeFilter));
    $maxAnalytics = max(1, $analytics->max(fn ($item) => max($item['issuances'], $item['materials'])));
@endphp

@section('body_class', 'portal-page portal-page-home')
@section('page_theme', 'pes')

@section('content')
    <div class="scroll-progress" id="scroll-progress"></div>

    @include('portal.partials.navigation')

    <main>
        @include('portal.partials.sections.hero')
        @include('portal.partials.sections.about')
        @include('portal.partials.sections.pes-action')
        @include('portal.partials.sections.whats-new')
        @include('portal.partials.sections.issuances')
        @include('portal.partials.sections.materials')
        @include('portal.partials.sections.dx')
        @include('portal.partials.sections.gates')
        @include('portal.partials.sections.contact')
    </main>

    @include('portal.partials.footer')
    @include('portal.partials.assistant')
    @include('portal.partials.issuance-modals')
@endsection
