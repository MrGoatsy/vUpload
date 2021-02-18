<script type="text/javascript">
    	$(function () {
            $('#btnSubmit').click(function() {
                $.ajax({
                    type: 'POST',
                    url: 'ajax.php?q=getComment&fileName=<?php echo $filename; ?>',
                    dataType: 'html',
                    success: function(data) {
                        $('#newUserComment').append('asd');
                        console.log(data);
                    },
                    async: true
                });	
            ;});
        });
</script>
<table>
        <tr>
            <td id="newUserComment"></td>
        </tr>
</table>