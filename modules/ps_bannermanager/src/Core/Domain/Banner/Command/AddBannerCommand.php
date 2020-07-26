<?php


namespace Bannermanager\Core\Domain\Banner\Command;


class AddBannerCommand
{


	private $title;

	private $active;

    private $description;

    private $image_name;

	private $show_title;

	private $single;

    private $parent;

    private $linkType;

    private $linkId;

	public function __construct(
        $title,
        $description,
        $active,
        $image_name,
        $show_title,
        $single,
        $parent,
        $linkType,
        $linkId
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->active = $active;
        $this->image_name = $image_name;
        $this->show_title = $show_title;
        $this->single = $single;
        $this->parent = $parent;
        $this->linkType = $linkType;
        $this->linkId = $linkId;
    }


    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription(){
        return $this->description;
    }


    public function getActive()
    {
        return $this->active;
    }

    public function getImageName(){
        return $this->image_name;
    }


    public function getShowTitle()
    {
        return $this->show_title;
    }

    public function getSingle()
    {
        return $this->single;
    }

    public function getParent(){
        return $this->parent;
    }


    public function getLinkType(){
        return $this->linkType;
    }

    public function getLinkId(){
        return $this->linkId;
    }
	
}