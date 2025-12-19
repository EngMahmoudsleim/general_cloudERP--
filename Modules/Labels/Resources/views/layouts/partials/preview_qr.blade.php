<table align="center" style="border-spacing: {{$barcode_details->col_distance * 1}}in {{$barcode_details->row_distance * 1}}in; overflow: hidden !important;">
    @foreach($page_products as $page_product)
    
        @if($loop->index % $barcode_details->stickers_in_one_row == 0)
            <!-- create a new row -->
            <tr>
            <!-- <columns column-count="{{$barcode_details->stickers_in_one_row}}" column-gap="{{$barcode_details->col_distance*1}}"> -->
        @endif
            <td align="center" valign="center">
                <div style="overflow: hidden !important;display: flex; flex-wrap: wrap;align-content: center;width: {{$barcode_details->width * 1}}in; height: {{$barcode_details->height * 1}}in; justify-content: center;">
                    <div>					
                        <img style="max-width:100% !important; display: block;" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($page_product->sub_sku, 'QRCODE', 6, 6) }}">					
                    </div>
                </div>		
            </td>
        @if($loop->iteration % $barcode_details->stickers_in_one_row == 0)
            </tr>
        @endif
    @endforeach
    </table>
    
    <style type="text/css">
    
        td{
            border: 1px dotted lightgray;
        }
        @media print{
            
            table{
                page-break-after: always;
            }
        
            @page {
            size: {{$paper_width}}in {{$paper_height}}in;
    
            /*width: {{$barcode_details->paper_width}}in !important;*/
            /*height:@if($barcode_details->paper_height != 0){{$barcode_details->paper_height}}in !important @else auto @endif;*/
            margin-top: {{$margin_top}}in !important;
            margin-bottom: {{$margin_top}}in !important;
            margin-left: {{$margin_left}}in !important;
            margin-right: {{$margin_left}}in !important;
        }
        }
    </style>