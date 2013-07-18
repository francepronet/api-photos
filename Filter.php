<?php

namespace Fpn\Prettifier\BackBundle\Controller;

use Fpn\ApiClient\Pictures\Filter;
use Fpn\ApiClient\Pictures\Preset;
use Guzzle\Http\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/filters", host="%admin_host%")
 */
class FiltersController extends Controller
{
    /**
     * @Route("/", name="admin_filters")
     * @Template()
     */
    public function indexAction()
    {
        $apiUrl = sprintf('http%s://%s:%s', ($this->container->getParameter('fpn_api_use_ssl') ? 's' : null), $this->container->getParameter('fpn_api_host'), $this->container->getParameter('fpn_api_port'));

        $client   = new Client($apiUrl);
        $request  = $client->get('/filters?page=1&limit=200000');
        $response = $request->send();

        $responseArray = $response->json();
        $items = $responseArray['items'];

        $filters = array();

        return array(
            'filters' => $filters,
        );
    }

    /**
     * @Route("/edit{filterId}", name="admin_filters_edit")
     * @Route("/add", name="admin_filters_add")
     * @Template()
     */
    public function editAction(Request $request, $filterId = null)
    {
        $filter = new Filter();
        $filter->setApiClient($this->get('fpn_api'));

        if (null !== $filterId) {
            $filter->fetch($filterId);
        }

        $preset = new Preset();
        $preset->setApiClient($this->get('fpn_api'));
        $_presets = $preset->fetchAll();
        $presets = array_combine(
            array_map(
                function ($preset) {
                    return $preset->getId();
                },
                $_presets
            ),
            array_map(
                function ($preset) {
                    return sprintf('%s - %s', $preset->getId(), $preset->getName());
                },
                $_presets
            )
        );

        $filterTypes = array(
            'resize',
            'resizeAndFill',
            'roundCorners',
            'addBorder',
            'addBanner',
            'addWatermark',
        );

        $form = $this->createFormBuilder($filter)
            ->add('presetId', 'choice', array(
                'choices' => $presets,
                'label' => 'Appartient au preset'
            ))
            ->add('type', 'choice', array(
                'label' => 'Type de filtre',
                'choices' => array_combine($filterTypes, $filterTypes),
            ))
            ->add('params', null, array('label' => 'Paramètres du filtre'))
            ->add('image', 'file', array('label' => 'Image', 'required' => false))
            ->add('Enregistrer', 'submit', array('attr' => array('class' => 'btn btn-success')))
            ->getForm()
            ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null != $filter->getImage() && $filter->getImage()->isValid()) {
                $tmpImage = realpath(sprintf('%s/%s', sys_get_temp_dir(), $filter->getImage()->getClientOriginalName()));

                $filter->getImage()->move(sys_get_temp_dir(), $filter->getImage()->getClientOriginalName());
                $filter->setImage($tmpImage);
            }
            $filter->save();

            if (isset($tmpImage)) {
                unlink($tmpImage);
            }

            $this->get('session')->getFlashBag()->add('success', 'Filtre enregistré avec succès.');

            return $this->redirect($this->generateUrl('admin_filters', array()));
        }

        return array(
            'form' => $form->createView(),
            'filterId' => $filterId,
        );
    }
}