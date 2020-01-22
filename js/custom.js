function get_image()
{
    $('#submit').click(function(){alert();
        var image = $('#image').value();
        var data = {
            'action': 'compress',
            'image': image,
        };
        $.post("compress.php", function(data){
            console.log(data)
        });
    });

}
    