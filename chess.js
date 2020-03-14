//Client
var move_from = null;
var move_to = null;
var turn = null;

$( document ).ready(function() { 
 
    //Initiate and load chessboard into div

    
    $.ajax({
       url: "./chess-mover.php",
       dataType: 'json',
       method: 'POST',
       success: function( result ) {          
            $('#chessboard').html(result.board);                
       },
       error: function( result ) {
         console.log(result.responseText);         
         alert('error occured');               
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
         obj.turn = turn;
         
         $.ajax({
            url: "./chess-mover.php",
            data: { movements : obj },
            dataType: 'json',
            method: 'POST',
            success: function( result ) {
               if ( result.board.length > 0) {
                 $('#chessboard').html(result.board);
                 $('#statusboard').html(result.status);
                 turn = result.turn;
                 move_from = null;
                 move_to = null;                 
                 //computermove(); 
               }
               else {
                  move_from = null;
                  move_to = null;    
                  $('.square').css('opacity',1);
                   alert('wrong move');
               }
            },
            error: function( result ) {
               console.log(result.responseText);
               move_from = null;
               move_to = null;      
               $('.square').css('opacity',1); 
               alert('error occured');               
            }
            
         });

      }
           
    });


   function computermove() {
      console.log('computermove now');
      var obj = {};
      if (turn == null) {turn = 0;}
      obj.turn = turn;

         $.ajax({
            url: "./chess-mover.php",
            data: { randommove : obj },
            dataType: 'json',
            method: 'POST',
            success: function( result ) {
               console.log(result);
               if ( result.board.length > 0) {
                  $('#chessboard').html(result.board);
                  $('#statusboard').html(result.status);
                  turn = result.turn;
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
               console.log(result.responseText);
               move_from = null;
               move_to = null;      
               $('.square').css('opacity',1); 
               alert('error occured');               
            }

         });

      }   

   //Start new game (reset game)
   $(document).on('click', '.reset', function() {
        var obj = {}
        obj.reset = 1;
        
        $.ajax({
           url: "./chess-mover.php",
           data: { startover : obj },
           dataType: 'json',
           method: 'POST',
           success: function( result ) {
                $('#chessboard').html(result.board);
                turn = result.turn;
                move_from = null;
                move_to = null;                  
           }
        });
   });    
});