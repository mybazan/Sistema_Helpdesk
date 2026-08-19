<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\{
    Equipo,
    PlanillaEquipoSistemaOperativo,
    PlanillaEquipoAcceso,
    PlanillaEquipoAlmacenamiento,
    PlanillaEquipo
};

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\{
    PlanillaEquipoDvrType,
    PlanillaEquipoPcType,
    PlanillaEquipoRelojBiometricoType,
    PlanillaEquipoCamaraType,
    PlanillaEquipoImpresoraType,
    PlanillaEquipoFotocopiadoraType,
    PlanillaEquipoIntercomunicadorType,
    PlanillaEquipoNotebookType,
    PlanillaEquipoServidorType,
};
use App\Repository\PlanillaEquipoRepository;
use App\Repository\EquipoRepository;



/**
 * @Route("/planillaEquipo")
 */
class PlanillaEquipoController extends AbstractController
{

    private $equipoRepository;
    private $planillaEquipoRepository;
    private $entityManager;
    private $paginator;
    private $encoders;
    private $normalizers;
    private $serializer;

    public function __construct(EquipoRepository $equipoRepository, PlanillaEquipoRepository $planillaEquipoRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->planillaEquipoRepository = $planillaEquipoRepository;
        $this->equipoRepository = $equipoRepository;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/new/{id}", name="new_planilla_equipo", methods={"GET","POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function newPlanilla(Request $request, Equipo $equipo, EntityManagerInterface $em): Response
    {
        if ($equipo->getPlanillaEquipo()) {
            $this->addFlash('warning', 'El equipo ya tiene una planilla. Use Editar Planilla para modificarla.');
            return $this->redirectToRoute('edit_planilla_equipo', [
                'id' => $equipo->getPlanillaEquipo()->getId()
            ]);
        }

        $planillaEquipo = new PlanillaEquipo();
        $tipoEquipo = $equipo->getTipo()->getNombre();
        [$formType, $template] = $this->getFormTypeAndTemplate($tipoEquipo);
        $form = $this->createForm($formType, $planillaEquipo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $planillaEquipo->setEquipo($equipo);

            try {
                $em->persist($planillaEquipo);
                $em->flush();

                $this->addFlash('success', 'Planilla creada correctamente.');
                return $this->redirectToRoute('show_equipo', [
                    'id' => $equipo->getId()
                ]);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al crear la planilla.');
            }
        }

        return $this->render('equipo/new_planilla.html.twig', [
            'planillaEquipo' => $planillaEquipo,
            'equipo' => $equipo,
            'form' => $form->createView(),
            'template' => $template,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_planilla_equipo", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function editPlanilla(Request $request, PlanillaEquipo $planillaEquipo): Response
    {
        if (!$planillaEquipo) {
            throw $this->createNotFoundException('No se encontró la planilla.');
        }

        // obtener el equipo asociado a la planilla
        $equipo = $planillaEquipo->getEquipo();

        if (!$equipo) {
            throw $this->createNotFoundException('No se encontró el equipo asociado a la planilla.');
        }

        // obtener el tipo de equipo
        $tipoEquipo = $equipo->getTipo()->getNombre();

        [$formType, $template] = $this->getFormTypeAndTemplate($tipoEquipo);
        $form = $this->createForm($formType, $planillaEquipo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $planillaEquipo->setEquipo($equipo);
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist($planillaEquipo);
                $em->flush();

                $this->addFlash("success", "Planilla editada correctamente.");
                return $this->redirectToRoute('show_equipo', [
                    'id' => $equipo->getId()
                ]);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al editar la planilla.');
            }
        }

        return $this->render('equipo/new_planilla.html.twig', [
            'planillaEquipo' => $planillaEquipo,
            'equipo' => $equipo,
            'template' => $template,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}", name="delete_planilla_equipo", methods={"POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function deletePlanilla(int $id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $planillaEquipo = $em->getRepository(PlanillaEquipo::class)->find($id);

        if (!$planillaEquipo) {
            throw $this->createNotFoundException('No se encontró la planilla.');
        }

        $equipo = $planillaEquipo->getEquipo();

        if (!$this->isCsrfTokenValid('delete_planilla' . $id, $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token de seguridad inválido.');
            return $this->redirectToRoute('show_equipo', [
                'id' => $equipo ? $equipo->getId() : null
            ], Response::HTTP_SEE_OTHER);
        }

        try {
            if ($equipo) {
                $equipo->setPlanillaEquipo(null);
                $em->persist($equipo);
            }
            $em->remove($planillaEquipo);
            $em->flush();

            $this->addFlash("success", "Planilla eliminada correctamente.");
        } catch (\Exception $e) {
            $this->addFlash("danger", "Error al eliminar la planilla.");
        }

        return $this->redirectToRoute('show_equipo', [
            'id' => $equipo ? $equipo->getId() : null
        ], Response::HTTP_SEE_OTHER);
    }

    /**
     * Obtiene el formulario y la plantilla correspondiente según el tipo de equipo.
     */
    private function getFormTypeAndTemplate(string $tipoEquipo): array
    {
        $formTypes = [
            "Cámara" => [PlanillaEquipoCamaraType::class, 'equipo/_form/_form_planilla_camara.html.twig'],
            "Camara" => [PlanillaEquipoCamaraType::class, 'equipo/_form/_form_planilla_camara.html.twig'],
            "DVR" => [PlanillaEquipoDvrType::class, 'equipo/_form/_form_planilla_dvr.html.twig'],
            "Fotocopiadora" => [PlanillaEquipoFotocopiadoraType::class, 'equipo/_form/_form_planilla_fotocopiadora.html.twig'],
            "Impresora" => [PlanillaEquipoImpresoraType::class, 'equipo/_form/_form_planilla_impresora.html.twig'],
            "Dispositivo de Interconexión" => [PlanillaEquipoIntercomunicadorType::class, 'equipo/_form/_form_planilla_interconexion.html.twig'],
            "Interconexión" => [PlanillaEquipoIntercomunicadorType::class, 'equipo/_form/_form_planilla_interconexion.html.twig'],
            "Notebook" => [PlanillaEquipoNotebookType::class, 'equipo/_form/_form_planilla_notebook.html.twig'],
            "Computadora de Escritorio" => [PlanillaEquipoPcType::class, 'equipo/_form/_form_planilla_pc.html.twig'],
            "Reloj Biométrico" => [PlanillaEquipoRelojBiometricoType::class, 'equipo/_form/_form_planilla_reloj_biometrico.html.twig'],
            "Reloj Biometrico" => [PlanillaEquipoRelojBiometricoType::class, 'equipo/_form/_form_planilla_reloj_biometrico.html.twig'],
            "Servidor" => [PlanillaEquipoServidorType::class, 'equipo/_form/_form_planilla_servidor.html.twig'],
        ];

        if (!array_key_exists($tipoEquipo, $formTypes)) {
            throw $this->createNotFoundException('Tipo de equipo no soportado para planilla.');
        }

        [$formType, $template] = $formTypes[$tipoEquipo];

        return [$formType, $template];
    }
}