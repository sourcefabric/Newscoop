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
    public static function Create($p_sessionId, &$p_objectId, $p_objectTypeId = null, $p_userId = null, $p_updateStats = false) {
        if (empty($p_sessionId)) {
            throw new SessionIdNotSet();
        }

        $session = new Session($p_sessionId);
        if (!$session->exists()) {
            $sessionParams = array('start_time'=>strftime("%Y-%m-%d %T"));
            if (!empty($p_userId)) {
                $sessionParams['user_id'] = $p_userId;
            }
            $session->create($sessionParams);
        }
        $sessionUserId = $session->getUserId();
        if (!empty($p_userId) && !empty($sessionUserId) && $sessionUserId != $p_userId) {
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

        if ($p_updateStats) {
            self::UpdateStats($p_sessionId, $p_objectId);
        }
    } // fn Create

    /**
     * Writes the statistics (when article read).
     *
     * @param int $p_sessionId
     *      used for not writing the stats multiple times
     * @param int $p_objectId
     *      the article object whose stats shall be updated
     * @return bool
     */
    public static function UpdateStats($p_sessionId, $p_objectId) {
        if ((!$p_sessionId) || (!$p_objectId)) {
            return false;
        }

        $request = new Request($p_sessionId, $p_objectId);
        if (!$request->exists()) {
            $request->create();
        }

        if (!$request->isInStats()) {
            $requestStats = new RequestStats($p_objectId);
            if (!$requestStats->exists()) {
                $requestStats->create();
            }
            $requestStats->incrementRequestCount();
            $request->setLastStatsUpdate();

            return true;
        }

        return false;
    } // fn UpdateStats

}

?>