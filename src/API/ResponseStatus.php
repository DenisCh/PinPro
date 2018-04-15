<?php
namespace Pinpro\API;

class ResponseStatus
{
    const PENDING = "PENDING";
    const OK = "OK";
    const FAIL = "FAIL";
    const REQUEST_INVALID = "REQUEST_INVALID";
    const AUTHENTICATION_FAILED = "AUTHENTICATION_FAILED";
    const NOT_FOUND = "NOT_FOUND";
    const TIME_OUT = "TIME_OUT";
    const TOO_MANY_REQUESTS = "TOO_MANY_REQUESTS";
    const REQUEST_FAILED = "REQUEST_FAILED";
}