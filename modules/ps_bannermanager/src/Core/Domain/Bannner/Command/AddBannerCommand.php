<?php


namespace Bannermanager\Core\Domain\Banner\Command;


class AddBannerCommand
{


	private $title;

	private $active;

	private $show_title;

	private $single;

	public function __construct(
        $title,
        $active,
        $show_title,
        $single
    ) {
        $this->title = $title;
        $this->active = $active;
        $this->show_title = $show_title;
        $this->single = $single;
    }


    public function getTitle()
    {
        return $this->title;
    }


    public function getActive()
    {
        return $this->active;
    }


    public function getShowTitle()
    {
        return $this->show_title;
    }

    public function getSingle()
    {
        return $this->single;
    }
	
}