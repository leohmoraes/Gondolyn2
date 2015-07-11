/*
|--------------------------------------------------------------------------
| On Module Load
|--------------------------------------------------------------------------
*/

$(function(){

    // Module Table Inline editing
    var striker;

    function _update (id) {

        _updateData = {};
        _updateData._token = _token;
        _updateData._id = id;

        _rowData = $('tr[data-id="'+id+'"]');

        _rowData.children('td').each(function(){
            var _column = $(this).children('input').attr('data-column');
            _updateData[_column] = $(this).children('input').val();
        });

        console.log(_updateData);

        $.ajax({
            type: "POST",
            url: _url+"/sample/edit",
            data: _updateData,
            cache: false,
            dataType: "html",
            success: function(data) {
                console.log(data)
            }
        });
    }

    $(".table-input").bind("keydown", function(e){
        var _id = $(this).parent().parent().attr('data-id');
        clearTimeout(striker);
        striker = setTimeout(function() {
            _update(_id);
        }, 600);
    });

});