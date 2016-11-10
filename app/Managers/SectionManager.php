<?php

namespace Managers;

use Repositories\UnitOfWork;
use Section;

class SectionManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getSectionsByTheme($themeId){
        return $this->_unitOfWork->sections()->getByTheme($themeId);
    }

    public function getSection($id){
        return $this->_unitOfWork->sections()->find($id);
    }

    public function addSection(Section $section, $themeId){
        $theme = $this->_unitOfWork->themes()->find($themeId);
        $section->setTheme($theme);

        $this->_unitOfWork->sections()->create($section);
        $this->_unitOfWork->commit();
    }

    public function updateSection(Section $section, $themeId){
        $theme = $this->_unitOfWork->themes()->find($themeId);
        $section->setTheme($theme);

        $this->_unitOfWork->sections()->update($section);
        $this->_unitOfWork->commit();
    }

    public function deleteSection($sectionId){
        $section = $this->_unitOfWork->sections()->find($sectionId);
        if ($section != null){
            $this->_unitOfWork->sections()->delete($section);
            $this->_unitOfWork->commit();
        }
    }


}