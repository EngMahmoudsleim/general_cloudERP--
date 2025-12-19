<!--#JCN pos_category_product_left contains the code to show the category in left side of products-->
<!--Show all categories-->
    <div class="main-category no-print tw-pt-1 active-all p-2 border tw-rounded-lg accordion-summary  block w-full transition-all duration-300 ease-in-out example-summary-class bg-gray-100 hover:bg-gray-200 cursor-pointer"
      data-value="all" data-parent="0"  >
      <div  class="tw-inline-flex tw-items-center " >
         <p class="text-nowrap hover:text-balance tw-text-xs">📱@lang('lang_v1.all_category')</p>
      </div>
    </div>

{{-- <div class="main-category no-print" data-value="all" data-parent="0">
        <div class="tw-bg-white hover:bg-gray-200 border p-2  active-all"  >
                <button type="button" >
                        <p class="text-nowrap hover:text-balance tw-text-xs">📱@lang('lang_v1.all_category')</p>
                </button> 
        </div>                                                
</div>  --}}
@foreach ($categories as $category)
  <div class="tw-pt-1  group accordion-item-wrapper {{-- example-class --}} {{-- border --}} border-gray-300 rounded-lg
    @if (empty($category['sub_categories'])) main-category {{-- cardpanel --}}  
    @else main-category-div {{-- cardpanel --}}   
    @endif no-print" data-value="{{ $category['id'] }}" data-name="{{ $category['name'] }}" data-parent="0">
    
    <div role="button" id="{{$category['id']}}" 
          class="tw-p-2 border tw-rounded-lg accordion-summary active-{{$category['id']}} block w-full transition-all duration-300 ease-in-out example-summary-class bg-gray-100 hover:bg-gray-200 cursor-pointer"
          @if (!empty($category['sub_categories'])) onclick="toggleAccordionContent(this, {{$category['id']}}) @endif" >
      <div  class=" tw-inline-flex tw-items-center">
        @if (!empty($category['sub_categories'])) {{--If there isnt empty has categories--}}
          <span id="icon-{{$category['id']}}" class="text-slate-800 transition-transform duration-300">
            ▷
          </span>        
        @endif
        <p class="text-nowrap hover:text-balance tw-text-xs">&nbsp;{{ $category['name'] }} </p>
      </div>
    </div>

    {{--SubCategories--}}
    <div data-collapse="collapse-1"
        class="rounded-lg invisible custom-accordion-content accordion-item-wrapper overflow-hidden transition-all duration-700 ease-in-out max-h-0 opacity-0 example-content-class">
      @if (!empty($category['sub_categories'])) {{--Isnt a parent category--}}
        @foreach ($category['sub_categories'] as $sc)
          <div class="tw-bg-white hover:bg-gray-200 border p-2 active-{{$sc['id']}} product_subcategory no-print" 
                data-maincategory="{{$category['id']}}" data-value="{{ $sc['id'] }}" 
                data-name="{{$sc['name']}}" data-parent="1" value="{{$sc['id']}}">
              <p class="text-nowrap hover:text-balance tw-text-xs"> {{ $sc['name'] }} </p>
          </div>
        @endforeach
      @endif
    </div>
  </div>
@endforeach

<script>
//#JCN 2025 function to Change the icon for subcategories when the category is in the left/middle
function toggleAccordionContent(element, index) {
      const content = element.nextElementSibling;
      const icon = document.getElementById(`icon-${index}`);
          // SVG for Down icon
      const downSVG = '▷';
 
      // SVG for Up icon
      const upSVG = '▽';

      if (content.classList.contains('invisible')) {
        content.classList.remove('invisible', 'max-h-0', 'opacity-0');
        content.classList.add('max-h-screen', 'opacity-100');
        icon.innerHTML = upSVG;
      } else {
        content.classList.add('invisible', 'max-h-0', 'opacity-0');
        content.classList.remove('max-h-screen', 'opacity-100');
        icon.innerHTML = downSVG;
      }
    }    

</script>

