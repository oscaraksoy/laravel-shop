@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.partials.cart_messages')
            @if(isset($products) && !empty($products->items))
            <h1>ZAWARTOŚĆ TWOJEGO KOSZYKA</h1>
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
                @foreach($products->items as $size => $product)
                    @foreach($product as $size => $single_product)
                    <tr>
                        <td data-column="Produkt" class="text-uppercase"><a href="{{ route('product', [$single_product['item']->id, str_slug($single_product['item']->name)]) }}">{{ $single_product['item']->name }}</a></td>
                        <td data-column="Zdjęcie podglądowe"><img src="{{ Voyager::image($single_product['item']->front_image) }}" width="100"></td>
                        <td data-column="Rozmiar">{{ $size }}</td>
                        <td data-column="Cena łączna" id="product{{ $single_product['item']->id }}{{ $size }}">{{ $single_product['price'] }} zł</td>
                        <td data-column="Cena jednostkowa">{{ $single_product['item']->discount_price ?? $single_product['item']->price }} zł</td>
                        <td data-column="Ilość">
                            <input data-url="{{ route('product.updateCart', [$single_product['item']->id, $size]) }}" class="quantity-input" style="width: 4em" type="number" value="{{ $single_product['qty'] }}">
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
                <h5 id="totalPrice" class="mt-5">DO ZAPŁATY: {{ $products->totalPrice }} zł</h5>
            @else
                <h5>KOSZYK JEST PUSTY</h5>
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

            $('.quantity-input').change(function(){
                var value=$(this).val();
                var url = $(this).attr('data-url');
                $.ajax({
                    type : 'get',
                    dataType: 'json',
                    url  : url,
                    data : {
                        'value':value,
                    },
                    success:function(data) {
                        $('#totalPrice').text('DO ZAPŁATY: ' + Math.round(data.totalPrice * 100) / 100 + " zł");
                        $('#totalQty').text(data.totalQty);
                        $('#product' + data.id + data.size).text(Math.round(data.price * 100) / 100 + " zł" );
                        $('.ajaxMessage').fadeIn('normal', function(){
                            $('.ajaxMessage').delay(1000).fadeOut();
                        });
                    },
                    error:function(data) {
                        console.log(data.error);
                    }
                });
            });

        });
    </script>
@stop