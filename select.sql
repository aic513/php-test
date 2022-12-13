SELECT COUNT(r.id) as all_count_requests
from request as r;

SELECT COUNT(rh.request_id) as filer_count_requests
from response_headers as rh
WHERE rh.header_key = 'Connection'
  AND rh.header_value = 'keep-alive';
