@extends('layouts.app')
@section('content')
@include('layouts.partials.cart_messages')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            @if(isset($products) && !empty($products->items))
            <h1 class="text-center">ZAWARTOŚĆ TWOJEGO KOSZYKA</h1>
            @if(session()->has('error'))
            <h5 class="text-center color-red">{{ session()->get('error') }}</h5>
            @endif
            <table class="mt-5">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Zdjęcie podglądowe</th>
                        <th>Rozmiar</th>
                        <th>Cena łączna</th>
                        <th>Cena jednostkowa</th>
                        <th>Ilość</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($products->items as $product)
                    @foreach($product as $size => $single_product)
                    <tr>
                        <td data-column="Produkt" class="text-uppercase"><a href="{{ route('product', [$single_product['item']->category->name, $single_product['item']->id, str_slug($single_product['item']->name)]) }}">{{ $single_product['item']->name }}</a></td>
                        <td data-column="Zdjęcie podglądowe"><img src="{{ Voyager::image($single_product['item']->front_image) }}" width="100"></td>
                        <td data-column="Rozmiar">{{ $size }}</td>
                        <td data-column="Cena łączna" id="product{{ $single_product['item']->id }}{{ $size }}">{{ $single_product['price'] }} zł</td>
                        <td data-column="Cena jednostkowa">{{ $single_product['item']->discount_price ?? $single_product['item']->price }} zł</td>
                        <td data-column="Ilość">
                            <input id="quantity{{ $single_product['item']->id }}{{ $size }}" data-url="{{ route('product.updateCart', [$single_product['item']->id, $size]) }}" class="quantity-input" style="width: 4em" type="number" max="{{ $single_product['item']->quantity }}" value="{{ $single_product['qty'] }}">
                        </td>
                        <form action="{{ route('product.deleteFromCart', [$single_product['item']->id, $size]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <td data-column="Akcje"><button class="btn btn-template" type="submit"><i class="fas fa-trash"></i> </button></td>
                        </form>
                    </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
                <div class="row mt-5">
                    <div class="col-md-12 text-right">
                        <h5 name="totalPrice" id="totalPrice">DO ZAPŁATY: {{ round($products->totalPrice * 100) / 100 }} zł</h5>
                        <a href="{{ route('product.order') }}"><button type="submit" class="btn btn-template mt-3">ZŁÓŻ ZAMÓWIENIE</button></a>
                    </div>
                </div>
            @else
                <h5 class="text-center">KOSZYK JEST PUSTY</h5>
            @endif
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        jQuery(document).ready(function(){
            jQuery('.toast__close').click(function(e){
                e.preventDefault();
                var parent = $(this).parent('.toast');
                parent.fadeOut("slow", function() { $(this).remove(); } );
            });
            @if(session()->has('cart_message'))
            $('.sessionMessage').fadeIn('slow', function(){
                $('.sessionMessage').delay(2000).fadeOut();
            });
            @endif
            $('#transferBtn').click(function() {
                checked = $("input[name='rules_confirmation']:checked").length;

                if(!checked) {
                    alert("Musisz zaakceptować regulamin!");
                    return false;
                }

            });

        });
    </script>
@stop