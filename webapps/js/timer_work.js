var secundomer = new (function() {
    var $stopwatch, // Stopwatch element on the page
        incrementTime = 70, // Timer speed in milliseconds
        currentTime = 0, // Current time in hundredths of a second
        updateTimer = function() {
            $stopwatch.html(formatTime(currentTime));
            currentTime += incrementTime / 10;
        },
        init = function() {
            $stopwatch = $('#clock1');
            secundomer.Timer = $.timer(updateTimer, incrementTime, false);
        };
    this.resetStopwatch = function() {
        currentTime = 0;
        this.Timer.stop();
    };
    $(init);
});