<?php


namespace Bannermanager\Core\Domain\Banner\Command;

use Bannermanager\Core\Domain\Banner\ValueObject\BannerId;
class EditBannerCommand
{


	private $bannerId;

	private $title;

	private $active;

	private $show_title;

	private $single;

	public function __construct($bannerId)
    {
        $this->bannerId = new BannerId($bannerId);
    }

    public function getBannerId()
    {
        return $this->bannerId;
    }


    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }


    public function getActive()
    {
        return $this->active;
    }


    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }


    public function getShowTitle()
    {
        return $this->show_title;
    }


    public function setShowTitle($show_title)
    {
        $this->show_title = $show_title;

        return $this;
    }

    public function getSingle()
    {
        return $this->single;
    }

    public function setSingle($single)
    {
        $this->single = $single;

        return $this;
    }
	
}