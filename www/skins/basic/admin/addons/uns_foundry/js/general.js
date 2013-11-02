/*
* Функция реализации добавления материалов
*
* */
$(function () {

$("img#on_cat").click(function (e) {
    $(this).parent().find("img#on_cat").addClass("hidden");
    $(this).parent().find("img#off_cat").removeClass("hidden");
    $("img.hand.plus").click();

});

$("img#off_cat").click(function (e) {
    $(this).parent().find("img#on_cat").removeClass("hidden");
    $(this).parent().find("img#off_cat").addClass("hidden");
    $("img.hand.minus").click();
});

$("img.hand.minus").click(function (e) {
    $(this).parent().find("img.hand.minus").addClass("hidden");
    $(this).parent().find("img.hand.plus").removeClass("hidden");
    $(this).parent().parent().parent().find("tr.category_items." + $(this).attr("category_items")).addClass("hidden");
});

$("img.hand.plus").click(function (e) {
    // Развернуть все
    $(this).parent().find("img.hand.plus").addClass("hidden");
    $(this).parent().find("img.hand.minus").removeClass("hidden");
    $(this).parent().parent().parent().find("tr.category_items." + $(this).attr("category_items")).removeClass("hidden");
});

});
