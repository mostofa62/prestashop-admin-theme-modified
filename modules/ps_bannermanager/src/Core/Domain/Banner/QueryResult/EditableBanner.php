<?php

namespace Bannermanager\Core\Domain\Banner\QueryResult;

use Bannermanager\Core\Domain\Banner\ValueObject\BannerId;
class EditableBanner
{
	
	private $bannerId;

	private $title;

    private $description;

    private $logo_image;

    private $image_name;

	private $active;

	private $show_title;

	private $single;

    private $parent;

    private $linkType;

    private $linkId;

	public function __construct(
		BannerId $bannerId,
		$title,
        $description,
        $image_name,
        $logo_image,
		$active,
		$show_title,
		$single,
        $parent,
        $linkType,
        $linkId
	)
    {
        $this->bannerId = $bannerId;
        $this->title = $title;
        $this->description = $description;
        $this->image_name =$image_name;
        $this->logo_image =$logo_image;
        $this->active = $active;
        $this->show_title = $show_title;
        $this->single = $single;
        $this->parent = $parent;
        $this->linkType = $linkType;
        $this->linkId = $linkId;
    }

    public function getBannerId()
    {
        return $this->bannerId;
    }


    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getImageName(){
        return $this->image_name;
    }


    public function getLogoImage(){
        return $this->logo_image;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function isShowTitle()
    {
        return $this->show_title;
    }

    public function isSingle()
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