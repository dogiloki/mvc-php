<?php
// $params -> variables enviadas por en método view()

echo json_encode($params['json']??[
    "status"=>false,
    "message"=>"No data found"
]);

?>