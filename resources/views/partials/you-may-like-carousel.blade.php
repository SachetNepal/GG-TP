@if (!empty($similarProducts) && collect($similarProducts)->isNotEmpty())
    @php
        $youMayLikeSlides = collect($similarProducts)->chunk(2);
    @endphp
    <aside class="card you-may-like-card you-may-like-card--under-media" aria-labelledby="you-may-like-heading">
        <h3 id="you-may-like-heading" class="you-may-like-card__title">You May Like</h3>

        <div class="you-may-like-carousel" data-you-may-like-carousel>
            <button type="button" class="you-may-like-arrow you-may-like-arrow--prev" aria-label="Previous suggestions" disabled>&lsaquo;</button>

            <div class="you-may-like-scroll" data-you-may-like-track tabindex="0" role="region" aria-label="Similar products">
                @foreach ($youMayLikeSlides as $pair)
                    <div class="you-may-like-slide">
                        <div class="you-may-like-slide-products">
                            @foreach ($pair as $similarProduct)
                                @include('partials.product-card-compact', ['product' => $similarProduct])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="you-may-like-arrow you-may-like-arrow--next" aria-label="Next suggestions">&rsaquo;</button>
        </div>
    </aside>
@endif
