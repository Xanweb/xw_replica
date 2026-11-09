$(function(){
    const openClass = 'rp-simple-accordion--open'
    const closeClass = 'rp-simple-accordion--closed'

    $(".js-rp-simple-accordion").click(function(){
        if($(this).parent().hasClass(openClass)){
            $(this).parent().removeClass(openClass).addClass(closeClass)
        }
        else if($(this).parent().hasClass(closeClass)){
            $(this).parent().removeClass(closeClass).addClass(openClass)
        }
    })
})
