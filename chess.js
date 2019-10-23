//Client
var move_from = null;
var move_to = null;

$( document ).ready(function() { 
    
    //Initiate and load chessboard into div
    $.ajax({
       url: "./chess-mover.php",
       dataType: 'html',
       method: 'POST',
       success: function( result ) {
          $('#chessboard').html(result);
       }
    });

   $("#chessboard").on('click', '.square', function() {
      var closest_square = $(this).closest('.square');
      closest_square.css('opacity', 0.5);   
      var square = {};
      square.x = closest_square.attr('data-x');
      square.y = closest_square.attr('data-y');

      if ( move_from == null ) {
         move_from = square;
      }
      else {
         move_to = square;

         obj = {};
         obj.x1 = move_from.x;
         obj.y1 = move_from.y;
         obj.x2 = move_to.x;
         obj.y2 = move_to.y;
         
         $.ajax({
            url: "./chess-mover.php",
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

   //Start new game (reset game)
   $(document).on('click', '.reset', function() {
        var obj = {}
        obj.reset = 1;
        
        $.ajax({
           url: "./chess-mover.php",
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