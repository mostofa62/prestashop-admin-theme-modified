<?php
/**
 * 2007-2019 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Bannermanager\Controller\Admin;


use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Bannermanager\Core\Search\Filters\LinkBlockFilters;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * This controller holds all custom actions which are added by extending "Sell > Customers" page.
 *
 * @see https://devdocs.prestashop.com/1.7/modules/concepts/controllers/admin-controllers/ for more details.
 */
class ListBannerController extends FrameworkBundleAdminController
{
    

    public function listAction(Request $request)
    {
        //Get hook list, then loop through hooks setting it in in the filter
        /** @var LinkBlockRepository $repository */
        $repository = $this->get('bannermanager.repository');
        $data = $repository->getBannerList();
        
        $filtersParams = $this->buildFiltersParamsByRequest($request);
        //$filtersParams = [];
        /** @var LinkBlockGridFactory $linkBlockGridFactory */
        
        $linkBlockGridFactory = $this->get('bannermanager.grid.factory');
        $grids = $linkBlockGridFactory->getGrids($data, $filtersParams);
        /**/
        $presentedGrids = [];
        
        foreach ($grids as $grid) {
            $presentedGrids[] = $this->presentGrid($grid);
        }



        return $this->render('@Modules/ps_bannermanager/views/templates/admin/list_banner/list.html.twig', [
            'grids' => $presentedGrids,
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    public function createAction(Request $request)
    {
        //$this->get('bannermanager.form_provider')->setIdBanner(null);
        //$form = $this->get('bannermanager.form_handler')->getForm();
        $bannerFormBuilder = $this->get('bannermanager.banner_form_builder');
        $form  = $bannerFormBuilder->getForm();
        

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        

            $data = $form->getData();
            //var_dump($data);die();
            $image_name = $this->uploadImage($data);
            $data['image_name']= $image_name;

            //var_dump($data);die();

            //$form->setData($data);
            $formHandler = $this->get('bannermanager.banner_form_handler');
        
            $result = $formHandler->handle($form);
        
            if (null !== $result->getIdentifiableObjectId()) {

                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_list_banner_list');

            }

        }

        //$this->processForm($form,$formHandler, 'Successful creation.');

        return $this->render('@Modules/ps_bannermanager/views/templates/admin/list_banner/form.html.twig', [
            'bannerForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);

    }

    public function editAction(Request $request, $bannerId)
    {
        //$this->get('bannermanager.form_provider')->setIdBanner($bannerId);
        //$form = $this->get('bannermanager.form_handler')->getForm();
        $bannerFormBuilder = $this->get('bannermanager.banner_form_builder');
        $form  = $bannerFormBuilder->getFormFor($bannerId);
        

        $form->handleRequest($request);

        $formHandler = $this->get('bannermanager.banner_form_handler');

        //$this->processForm($form,$formHandler, 'Successful update.', $bannerId);
        $result = $formHandler->handleFor($bannerId, $form);
        
        if ($result->isSubmitted() && $result->isValid()) {
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_list_banner_list');
        }

        return $this->render('@Modules/ps_bannermanager/views/templates/admin/list_banner/form.html.twig', [
            'bannerForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }
    /*
    public function createProcessAction(Request $request)
    {
        return $this->processForm($request, 'Successful creation.');
    }

    public function editProcessAction(Request $request, $bannerId)
    {
        return $this->processForm($request, 'Successful update.', $bannerId);
    }*/


    private function processForm($form, $formHandler, $successMessage, $BannerId = null)
    {
        /** @var LinkBlockFormDataProvider $formProvider */
        //$formProvider = $this->get('bannermanager.form_provider');
        //$formProvider->setIdBanner($BannerId);

        /** @var FormHandlerInterface $formHandler */
        
        //$form = isset($BannerId)?$formHandler->getFormFor($BannerId): $formHandler->getForm();
        


        if ($form->isSubmitted() && $form->isValid()) {
            

            $data = $form->getData();
            //$this->uploadImage($data);            
            $saveErrors = $formHandler->save($data);
            //$saveErrors = [] ;
            

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans($successMessage, 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_list_banner_list');
            }

            $this->flashErrors($saveErrors);
        }

        /*

        return $this->render('@Modules/ps_bannermanager/views/templates/admin/list_banner/form.html.twig', [
            'bannerForm' => $form->createView(),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);*/
    }

    private function uploadImage(array $params)
    {
        /** @var ImageUploaderInterface $supplierExtraImageUploader */
        $bannerImageUploader = $this->get(
            'bannermanager.banner_image_uploader'
        );

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $params['image_name'];

        if ($uploadedFile instanceof UploadedFile) {
            $bannerImageUploader->upload($params['id_banner'], $uploadedFile);
            return $uploadedFile->getClientOriginalName();
        }

        return null;
        
    }


    private function getToolbarButtons()
    {
        return [
            'add' => [
                'href' => $this->generateUrl('admin_banner_create'),
                'desc' => $this->trans('New Banner', 'Modules.Banner.Admin'),
                'icon' => 'add_circle_outline',
            ],
        ];
    }

    private function buildFiltersParamsByRequest(Request $request)
    {
        $filtersParams = array_merge(LinkBlockFilters::getDefaults(), $request->query->all());
        //$filtersParams['filters']['title'] = $this->getContext()->language->id;

        return $filtersParams;
    }


}
