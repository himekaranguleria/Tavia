/* coustom code swap
 * 
 * Functions
 * tempons_sum ---- for the sum funcnality of first page
 * 
 * 
 * ---------------------------------------------------------------------------
 * classes
 * s_item ---- all the single items
 * t_item ---- all the single Tempons items
 * p_item ---- all the single Pads items
 * l_item ---- all the single Liners items
 * tr_class --- Class name of table TR
 * section_total --- category totals 
 * section_total_sum --- Complete totals of all
 * 
 * 
 * ---------------------------------------------------------------------------
 * Variables
 * sum --- sum
 * 
 */

//page 1 function
require(['jquery', 'jquery/ui'], function ($) {
//    function tempons_sum(tr_class) {
//        var sum = 0;
//        var sumcat = 0;
//        $.each($("tr." + tr_class + " td.plus_minus .s_item"), function (key, value) {
//            sum += +$(value).val();
//        });
//        console.log("." + tr_class + "_sum_text");
//        $("." + tr_class + "_sum_val").val(sum);
//        $("." + tr_class + "_sum_text").text(sum);
//        $(".section_total").each(function () {
//            sumcat += +$(this).val();
//        });
//        $(".section_total_sum").val(sumcat);
//    }

//    (function ($) {
//        $(document).ready(function () {
//            $(".container-fluid").hide();
//            $(".tab-header").show();
//
//            //page 1 section plus minus code
//            $(".incr-btn").on("click", function (e) {
//                var $button = $(this);
//                var oldValue = $button.parent().find('.s_item').val();
//                $button.parent().find('.incr-btn[data-action="decrease"]').removeClass('inactive');
//                if ($button.data('action') == "increase") {
//                    var newVal = parseFloat(oldValue) + 1;
//                } else {
//                    if (oldValue > 0) {
//                        var newVal = parseFloat(oldValue) - 1;
//                    } else {
//                        newVal = 0;
//                        $button.addClass('inactive');
//                    }
//                }
//                $button.parent().find('.s_item').val(newVal);
//                tempons_sum($button.parent().parent().attr("class"))
//                e.preventDefault();
//            });
//            // page 1 getting values on change of tempons
//            $(document).on("change", ".t_item", function () {
//                var sum = 0;
//                $(".t_item").each(function () {
//                    sum += +$(this).val();
//                });
//            });
//        });
//    }(jQuery));
});