/*
|--------------------------------------------------------------------------
| Menu Highlighter
|--------------------------------------------------------------------------
*/

$('.nav-sidebar li').each(function(){
    if ($(this).children('a').text().toLowerCase().trim() == $('.page-header').text().toLowerCase().trim()) {
        $(this).addClass('active');
    }
});