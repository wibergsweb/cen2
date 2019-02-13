//Client
var move_from = null;
var move_to = null;

$( document ).ready(function() { 
    
    //Initiate and load chessboard into div
    $.ajax({
       url: "//chessengine/chess-html.php",
       dataType: 'html',
       method: 'POST',
       success: function( result ) {
          $('#chessboard').html(result);
       }
    });

  
  $("#chessboard").on('click', '.square', function() {
    var closest_square = $(this).closest('.square');
    closest_square.css('opacity', 0.5);   
    var id_square = closest_square.attr('id');
    var name_square = id_square.split('-');
    var index = name_square[1];
    if ( move_from == null ) {
        move_from = index;
    }
    else {
        move_to = index;
        var obj = {}
        obj.from = move_from;
        obj.to = move_to;

        $.ajax({
           url: "//chessengine/chess-html.php",
           data: { movements : obj },
           dataType: 'html',
           method: 'POST',
           success: function( result ) {
              if ( result.length > 0) {
                $('#chessboard').html(result);
                move_from = null;
                move_to = null;                  
              }
              else {
                move_from = null;
                move_to = null;    
                $('.square').css('opacity',1);
                  alert('wrong move');
              }
           },
           error: function( result ) {
                move_from = null;
                move_to = null;      
                $('.square').css('opacity',1); 
                alert('error occured');               
           }
           
        });

    }

    });
    
  $(document).on('click', '.reset', function() {
        var obj = {}
        obj.reset = 1;
        
        $.ajax({
           url: "//chessengine/chess-html.php",
           data: { startover : obj },
           dataType: 'html',
           method: 'POST',
           success: function( result ) {
                $('#chessboard').html(result);
                move_from = null;
                move_to = null;                  
           }
        });

    });    
});