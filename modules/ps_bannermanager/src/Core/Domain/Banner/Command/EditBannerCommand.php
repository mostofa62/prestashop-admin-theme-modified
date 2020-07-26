<?php


namespace Bannermanager\Core\Domain\Banner\Command;

use Bannermanager\Core\Domain\Banner\ValueObject\BannerId;
class EditBannerCommand
{


	private $bannerId;

	private $title;

    private $description;

    private $image_name;

	private $active;

	private $show_title;

	private $single;

    private $parent;

    private $linkType;

    private $linkId;

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

    public function getDescription(){
        return $this->description;
    }


    public function setDescription($description){
        
        $this->description = $description;
        
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

    public function getImageName()
    {
        return $this->image_name;
    }


    public function setImageName($image_name)
    {
        $this->image_name = $image_name;

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


    public function getParent(){
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getLinkType(){
        return $this->linkType;
    }

    public function setLinkType($linkType)
    {
        $this->linkType = $linkType;

        return $this;
    }


    public function getLinkId(){
        return $this->linkId;
    }

    public function setLinkId($linkId)
    {
        $this->linkId = $linkId;

        return $this;
    }
	
}