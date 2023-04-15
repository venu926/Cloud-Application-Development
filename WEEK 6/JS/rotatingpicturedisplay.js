//show the page only after all media is loaded
$(window).load
(function()
  {
    var rotpicDisplay = 
    {
      init: function()
      {
        //declare variables
        var initialize = 0;
        var firstAppear = 500;
	var nextAppear = 1500;
        var dispTime = 2500;
        var counter = $('.images').length;
        //begin with first picture in rotating diplay
        $('.images').eq(initialize).fadeIn(firstAppear);
        //loop to create a rotating display
        var rotpicLoop = setInterval
        (function()
        {
          $('.images').eq(initialize).fadeOut(nextAppear);
          if(initialize == counter - 1)
          {
            initialize = 0;
          }
          else
          {
            initialize++;
          }
          $('.images').eq(initialize).fadeIn(nextAppear);
        }, dispTime);
      }
    };
    rotpicDisplay.init();
  }
);
