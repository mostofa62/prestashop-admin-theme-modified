<?php

namespace Bannermanager\Controller\Admin;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Bannermanager\Core\Search\Filters\BannerFilters;
use Bannermanager\Core\Grid\Definition\Factory\BannerGridDefinationFactory;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


use Bannermanager\Core\Domain\Banner\Query\GetBannerForEditing;

class BannerController extends FrameworkBundleAdminController
{
    /**
     * @return Response
     */
    public function indexAction(
        Request $request,
        BannerFilters $bannerFilters
    )
    {
        
        
    
        $bannerGridFactory = $this->get('banners.core.grid.banner_grid_factory');
        $bannerGrid = $bannerGridFactory->getGrid($bannerFilters);
        
        return $this->render('@Modules/ps_bannermanager/views/templates/admin/banner/index.html.twig', [
            'help_link' =>$this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'bannersGrid' => $this->presentGrid($bannerGrid),
        ]);
    }


    public function searchAction(Request $request)
    {
        $gridDefinitionFactory = 'banners.core.grid.banner_grid_defination_factory';
        $filterId = BannerGridDefinationFactory::GRID_ID;
        

        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get($gridDefinitionFactory),
            $request,
            $filterId,
            'banner_list'
        );
    }


    public function createAction(Request $request)
    {
        try {
            //$bannerFormBuilder = $this->getFormBuilder();            
            //$bannerForm = $bannerFormBuilder->getForm();
            //$this->formBuilderSubmit($bannerFormBuilder);
            $bannerForm = $this->getFormBuilder()->getForm();
            $bannerForm->handleRequest($request);

            $result = $this->getFormHandler()->handle($bannerForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('banner_list');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@Modules/ps_bannermanager/views/templates/admin/banner/add.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'bannerForm' => $bannerForm->createView(),
        ]);
    }


    public function editAction(Request $request, $bannerId)
    {
        try {
            $bannerForm = $this->getFormBuilder()->getFormFor((int) $bannerId);
            $bannerForm->handleRequest($request);

            $result = $this->getFormHandler()->handleFor((int) $bannerId, $bannerForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('banner_list');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof BannerNotFoundException) {
                return $this->redirectToRoute('banner_list');
            }
        }

        /** @var EditableBanner $editableBanner */
        $editableBanner = $this->getQueryBus()->handle(new GetBannerForEditing((int) $bannerId));

        return $this->render('@Modules/ps_bannermanager/views/templates/admin/banner/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'bannerForm' => $bannerForm->createView(),
            'bannerName' => $editableBanner->getTitle(),
            'logoImage' => $editableBanner->getLogoImage(),
        ]);
    }


    private function getFormBuilder()
    {
        return $this->get('banners.form.identifiable_object.builder.banner_form_builder');
    }

    private function getFormHandler()
    {
        return $this->get('banners.form.identifiable_object.handler.banner_form_handler');
    }
    /*
    private function formBuilderSubmit($formBuilder){

        $formBuilder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
            
            $data = $event->getData();
            //var_dump($data);die();
            $data['image_name'] = date('Ymdhi').'.jpg';
            $form = $event->getForm();
            $event->setData($data); 
            
            
        });

    }*/
}
