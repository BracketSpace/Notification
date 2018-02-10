<?php

namespace underDEV\Notification\Abstracts;

use underDEV\Notification\Interfaces;

abstract class Common implements Interfaces\Nameable {

	/**
     * Objecy slug
     *
     * @var string
     */
    protected $slug;

    /**
     * Human readable, translated name
     *
     * @var string
     */
    protected $name;

    /**
     * Gets slug
     *
     * @return string slug
     */
    public function get_slug() {
    	return $this->slug;
    }

    /**
     * Gets name
     *
     * @return string name
     */
    public function get_name() {
    	return $this->name;
    }

}
