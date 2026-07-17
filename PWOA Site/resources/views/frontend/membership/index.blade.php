@extends('layouts.front')

@section('title', 'Membership Plans')

@section('content')
<section class="section-hero bg-brand-gradient">
    <div class="container text-center">
        <span class="badge rounded-pill badge-soft-primary px-3 py-2 mb-3">Membership</span>
        <h1 class="display-5 fw-bold mb-3">Choose the membership that fits your business.</h1>
        <p class="lead text-white-50 mx-auto" style="max-width: 760px;">Join PWOA for visibility, education, event benefits, and stronger support as you grow.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-5">
                <div class="card card-soft h-100">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h3 mb-2">Standard Membership</h2>
                        <p class="display-5 fw-bold mb-3">$99 <span class="fs-6 text-secondary">/ year</span></p>
                        <ul class="text-secondary mb-4">
                            <li class="mb-2">Member directory listing</li>
                            <li class="mb-2">Compliance resource access</li>
                            <li class="mb-2">Core education access</li>
                            <li class="mb-2">Member event discounts</li>
                            <li>PWOA token participation rewards</li>
                        </ul>
                        <form method="POST" action="{{ route('membership.subscribe') }}">@csrf<input type="hidden" name="plan" value="standard"><button type="submit" class="btn btn-brand btn-lg w-100">Join as Standard</button></form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card card-soft h-100 border-warning border-2">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex justify-content-between align-items-center mb-2"><h2 class="h3 mb-0">Gold Membership</h2><span class="badge text-bg-warning">Best Value</span></div>
                        <p class="display-5 fw-bold mb-3">$299 <span class="fs-6 text-secondary">/ year</span></p>
                        <ul class="text-secondary mb-4">
                            <li class="mb-2">Everything in Standard</li>
                            <li class="mb-2">Priority directory placement</li>
                            <li class="mb-2">Unlimited course access</li>
                            <li class="mb-2">Higher event discounts</li>
                            <li class="mb-2">Priority support and stronger rewards</li>
                        </ul>
                        <form method="POST" action="{{ route('membership.subscribe') }}">@csrf<input type="hidden" name="plan" value="gold"><button type="submit" class="btn btn-accent btn-lg w-100">Join as Gold</button></form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-5">
            <div class="col-lg-10">
                <div class="text-center mb-4">
                    <span class="badge rounded-pill badge-soft-primary px-3 py-2 mb-3">FAQ</span>
                    <h2 class="h3 fw-bold">Frequently Asked Questions</h2>
                    <p class="text-muted">Find answers to common questions about PWOA membership.</p>
                </div>

                @if($faqCategories->count())
                    {{-- Category Pills --}}
                    <ul class="nav nav-pills justify-content-center gap-2 mb-4" id="faqTab" role="tablist">
                        @foreach($faqCategories as $catIndex => $faqCat)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-2 {{ $catIndex === 0 ? 'active' : '' }}"
                                    id="faq-tab-{{ $faqCat->id }}"
                                    data-bs-toggle="pill"
                                    data-bs-target="#faq-pane-{{ $faqCat->id }}"
                                    type="button" role="tab">
                                    @if($faqCat->icon)
                                        <i class="bi {{ $faqCat->icon }} me-1"></i>
                                    @endif
                                    {{ $faqCat->name }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Category Tab Content --}}
                    <div class="tab-content" id="faqTabContent">
                        @foreach($faqCategories as $catIndex => $faqCat)
                            <div class="tab-pane fade {{ $catIndex === 0 ? 'show active' : '' }}"
                                id="faq-pane-{{ $faqCat->id }}" role="tabpanel">
                                <div class="accordion" id="faqAccordion{{ $faqCat->id }}">
                                    @foreach($faqCat->faqs as $faqIndex => $faq)
                                        <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden shadow-sm">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button {{ $faqIndex > 0 ? 'collapsed' : '' }} fw-bold"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#faq-{{ $faq->id }}">
                                                    {{ $faq->question }}
                                                </button>
                                            </h2>
                                            <div id="faq-{{ $faq->id }}"
                                                class="accordion-collapse collapse {{ $faqIndex === 0 ? 'show' : '' }}"
                                                data-bs-parent="#faqAccordion{{ $faqCat->id }}">
                                                <div class="accordion-body text-muted">
                                                    {!! $faq->answer !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <p>No FAQs available yet. Check back soon!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
