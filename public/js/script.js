$(document).ready(function() {
    startMovingFiles();
});
function startMovingFiles()
{
    $('.startMovingFilesB').bind('click', function() {
        var $this = $(this);
        $.ajax({
            type: 'GET',
            url: 'publisher.php',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success: function(response) {
                switch (response.status) {
                    case 'ok' :
                        $('.progressBarDiv').removeClass('hide');
                        $('.errorDiv, .warningDiv').addClass('hide');
                        $this.prop('disabled', true);
                        progress();
                        break;
                    case 'error' :
                        $('.errorDiv').removeClass('hide').text(response.msg);
                        $('.progressBarDiv, .warningDiv').addClass('hide');
                        break;
                    case 'empty' :
                        $('.warningDiv').removeClass('hide').text(response.msg);
                        $('.progressBarDiv, .errorDiv').addClass('hide');
                        break;
                }
            }
        });
    });
}
function progress()
{
    // Replace with your hostname
    var ws = new SockJS('http://localhost:15674/stomp');
    var client = Stomp.over(ws);

    // RabbitMQ SockJS does not support heartbeats so disable them
    client.heartbeat.outgoing = 0;
    client.heartbeat.incoming = 0;

    client.debug = onDebug;

    // Make sure the user has limited access rights
    client.connect('guest', 'guest', onConnect, onError);

    function onConnect()
    {
        var size = 0;
        var completeness = 0;
        var count = 0;
        var id = client.subscribe('/exchange/after_moving_file', function(d) {
            var message = jQuery.parseJSON(d.body);
            size += message.size;
            $('.fileCounter b').text(++count);
            completeness = Math.ceil((size / message.sumFilesSize) * 100);
            $('.progress-bar').attr('aria-valuenow', completeness).css({'width': completeness + '%'}).
                find('span').text(completeness + '% Complete')
        });
    }

    function onError(e)
    {
        console.log('STOMP ERROR', e);
    }

    function onDebug(m)
    {
        console.log('STOMP DEBUG', m);
    }
}