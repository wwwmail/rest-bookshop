<?php

namespace Application\Web;

class Response extends AbstractHttp {

    public function __construct(Request $request = NULL, $status = NULL, $contentType = NULL)
    {
        if ($request) {
            $this->uri = $request->getUri();
            $this->data = $request->getData();
            $this->method = $request->getMethod();
            $this->cookies = $request->getCookies();
            $this->setTransport();
            $this->filter = $this->getFilter();
            $this->filterData = $this->getFilterData();
        }
        $this->processHeaders($contentType);
        if ($status) {
            $this->setStatus($status);
        }
    }

    protected function processHeaders($contentType)
    {
        if (!$contentType) {
            $this->setHeaderByKey(self::HEADER_CONTENT_TYPE, self::CONTENT_TYPE_JSON);
        } else {
            $this->setHeaderByKey(self::HEADER_CONTENT_TYPE, $contentType);
        }
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

}
