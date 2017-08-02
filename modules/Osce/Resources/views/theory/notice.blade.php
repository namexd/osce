<script>
   	$(function () {
   		var msg = {!! json_encode($errors) !!};
   		if (msg) {
   			for (var name in msg) {
   				if (name==0||name=='error') {
   					uselayer(3,msg[name][0]);
   				} else if (name==1||name=='success') {
   					uselayer(31,msg[name][0]);
   				}
   			}	   				
   		}   			
	});
</script>
