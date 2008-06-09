<?php

class SessionIdNotSet extends Exception {
    public function __construct() {
        parent::__construct('The session identifier was not set.', null);
    }
}

class ObjectIdNotSet extends Exception {
    public function __construct() {
        parent::__construct('The object identifier was not set.', null);
    }
}

class ObjectTypeIdNotSet extends Exception {
    public function __construct() {
        parent::__construct('The object type identifier was not specified for the new object.', null);
    }
}

class InvalidUserId extends Exception {
    public function __construct() {
        parent::__construct('The specified user identifier does not match the session user identifier.', null);
    }
}

class SessionRequest {
    public static function Create($p_sessionId, &$p_objectId, $p_objectTypeId = null, $p_userId = null) {
        if (empty($p_sessionId)) {
            throw new SessionIdNotSet();
        }

        $session = new Session($p_sessionId);
        if (!$session->exists()) {
            $session->create(array('start_time'=>time(), 'user_id'=>$p_userId));
        }
        if (!empty($p_userId) && $session->getUserId() != $p_userId) {
            throw new InvalidUserId();
        }

        $requestObject = new RequestObject($p_objectId);
        if (!$requestObject->exists()) {
            if (empty($p_objectTypeId)) {
                throw new ObjectTypeIdNotSet();
            }
            $requestObject->create(array('object_type_id'=>$p_objectTypeId));
            $p_objectId = $requestObject->getObjectId();
        } elseif (empty($p_objectId)) {
            throw new ObjectIdNotSet();
        }

        $request = new Request($p_sessionId, $p_objectId);
        if (!$request->exists()) {
            $request->create(array('request_count'=>1));
        }
    }
}

?>