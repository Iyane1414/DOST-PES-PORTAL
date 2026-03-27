@extends('layouts.app', ['title' => 'DOST PES Portal'])

@php
    $issuanceSearch = strtolower($search);
    $materialSearchTerm = strtolower($materialSearch);
    $filteredIssuances = $issuances->filter(fn ($item) => ($issuanceSearch === '' || str_contains(strtolower($item->title.' '.$item->division), $issuanceSearch)) && ($categoryFilter === '' || $categoryFilter === 'All' || $item->category === $categoryFilter));
    $filteredMaterials = $materials->filter(fn ($item) => ($materialSearchTerm === '' || str_contains(strtolower($item->title.' '.$item->type.' '.$item->division), $materialSearchTerm)) && ($materialTypeFilter === '' || $materialTypeFilter === 'All' || $item->type === $materialTypeFilter));
    $maxAnalytics = max(1, $analytics->max(fn ($item) => max($item['issuances'], $item['materials'])));
@endphp

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
        @include('portal.partials.sections.contact')
        @include('portal.partials.sections.subscribe')
    </main>

    @include('portal.partials.footer')
    @include('portal.partials.assistant')
    @include('portal.partials.issuance-modals')
@endsection
