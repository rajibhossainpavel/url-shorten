1. Stor URL
============
URL: localhost:8000/store-url
==== 
Method: POST
=======
Body:
====
url:https://stackoverflow.com/questions/6019853/php-cannot-use-a-scalar-as-an-array-warning
//single_use:1
//valid_for_days:2

Result: 
=======
{
    "status": 200,
    "data": {
        "headers": {},
        "original": {
            "status": 200,
            "success": true,
            "data": {
                "short_url": "http://localhost:8000/short/dzEMzB",
                "url_key": "dzEMzB"
            }
        },
        "exception": null
    }
}

2. Show URL
============
URL: localhost:8000/show-url
==== 
Method: POST
=======
Body:
====
short_url:http://localhost:8000/short/dzEMzB

Result: 
=======
{
    "status": 200,
    "data": {
        "headers": {},
        "original": {
            "status": 200,
            "success": true,
            "data": {
                "destination_url": "https://stackoverflow.com/questions/6019853/php-cannot-use-a-scalar-as-an-array-warning",
                "short_url": "http://localhost:8000/short/dzEMzB"
            }
        },
        "exception": null
    }
}

3. Visit URL
============
URL: http://localhost:8000/short/dzEMzB
==== 
Method: GET
=======
Body:
====


Result: 
=======
This goes to the original URL.

4. Track URL
============
URL: localhost:8000/track-url
==== 
Method: POST
=======
Body:
====
short_url:http://localhost:8000/short/dzEMzB

Result: 
=======
{
    "status": 200,
    "data": {
        "headers": {},
        "original": {
            "status": 200,
            "success": true,
            "data": {
                "destination_url": "https://stackoverflow.com/questions/6019853/php-cannot-use-a-scalar-as-an-array-warning",
                "short_url": "http://localhost:8000/short/dzEMzB"
            }
        },
        "exception": null
    }
}
