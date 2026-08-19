<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Repository\PedidoRepository;
use App\Repository\PedidoEquipoRepository;
use App\Repository\PedidoEstadoRepository;
use App\Repository\PedidoHistorialEstadoRepository;
use App\Repository\UserRepository;
use App\Repository\PedidoTecnicoAsignadoRepository;

use App\Form\pedido\FiltroType;
use App\Form\PedidoFormType;

use Dompdf\Dompdf;

use App\Entity\Pedido;
use App\Entity\PedidoEquipo;
use App\Entity\PedidoHistorialEstado;
use App\Entity\PedidoEstado;
use App\Entity\PedidoTecnicoAsignado;

use App\Services\Upload;
use Twig\Environment;

class PedidoController extends AbstractController
{
    private $paginator;
    private $entityManager;
    private $pedidoRepository;
    private $pedidoEquipoRepository;
    private $pedidoEstadoRepository;
    private $pedidoHistorialEstadoRepository;
    private $userRepository;
    private $serviceUpload;


    public function __construct(
        PedidoRepository $pedidoRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        PedidoEstadoRepository $pedidoEstadoRepository,
        PedidoHistorialEstadoRepository $pedidoHistorialEstadoRepository,
        Upload $serviceUpload,
        PedidoEquipoRepository $pedidoEquipoRepository
    ) {
        setlocale(LC_TIME, 'es_ES.utf8', 'es_ES', 'spanish');
        $this->pedidoRepository = $pedidoRepository;
        $this->pedidoEquipoRepository = $pedidoEquipoRepository;
        $this->pedidoEstadoRepository = $pedidoEstadoRepository;
        $this->pedidoHistorialEstadoRepository = $pedidoHistorialEstadoRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->serviceUpload = $serviceUpload;
    }

    /**
     * @Route("/ticket", name="ticket_index")
     * @IsGranted("TICKET_VER")
     */
    public function index(Request $request, Security $security): Response
    {
        $formFiltros = $this->createForm(FiltroType::class);
        if ($request->query->get($formFiltros->getName())) {
            $formFiltros->handleRequest($request);
        }
        $filtros = $formFiltros->getData();

        if (!$security->isGranted('TICKET_ASIGNAR') && parent::getUser()->getIsTecnico()) {
            $filtros['asignado'] = parent::getUser()->getId();
        }

        $pagination = $this->paginator->paginate(
            $this->pedidoRepository->findForActionIndex($filtros),
            $request->query->get('page', 1),
            12
        );

        return $this->render('pedido/index.html.twig', [
            'pagination' => $pagination,
            'formFiltros' => $formFiltros->createView(),
            'usuario' => parent::getUser(),
            'tecnicos' => $this->userRepository->findTecnicos()
        ]);
    }
    /**
     * @Route("/ticket/new", name="ticket_new", methods={"GET","POST"})
     * @IsGranted("TICKET_CREAR")
     */
    public function new(Request $request): Response
    {
        $pedido = new Pedido();
        $form = $this->createForm(PedidoFormType::class, $pedido);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $this->sincronizarUbicacionTexto($pedido);
            $pedido->setFecha(new \Datetime());
            $em->persist($pedido);

            $historial = new PedidoHistorialEstado();
            $historial->setPedido($pedido);
            $historial->setFecha(new \Datetime());
            $historial->setPedidoEstado($this->requirePedidoEstado('Recibido'));
            $historial->setUsuario(parent::getUser());
            $em->persist($historial);

            $em->flush();

            $this->addFlash("success", "Ticket de soporte Nº " . $pedido->getId() . " generado correctamente.");
            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('pedido/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/ticket/edit/{id}", name="ticket_edit", requirements={"id":"\d+"})
     * @IsGranted("TICKET_EDITAR")
     */
    public function edit(Pedido $pedido, Request $request)
    {
        if ($this->getUser()->getIsTecnico() && !$this->isGranted('TICKET_ASIGNAR')) {
            throw $this->createAccessDeniedException('Los técnicos no pueden editar el contenido del ticket.');
        }

        $form = $this->createForm(PedidoFormType::class, $pedido);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->sincronizarUbicacionTexto($pedido);
            $em->persist($pedido);

            $em->flush();

            $this->addFlash("success", "Ticket de soporte Nº" . $pedido->getId() . " editado correctamente.");
            return $this->redirectToRoute('ticket_show', [
                'id' => $pedido->getId()
            ]);
        }

        return $this->render("pedido/edit.html.twig", [
            "form"      => $form->createView(),
            "pedido"    => $pedido,
        ]);
    }

    /**
     * @Route("/ticket/delete/{id}",name="ticket_delete", requirements={"id":"\d+"})
     * @IsGranted("TICKET_ELIMINAR")
     */
    public function delete(Pedido $pedido)
    {

        try {
            // Verificar si se puede eliminar el pedido
            if ($this->canDeletePedido($pedido)) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($pedido);
                $entityManager->flush();

                $this->addFlash('success', 'El pedido fue eliminado correctamente.');
            } else {
                // No se puede eliminar el pedido según las reglas definidas
                $this->addFlash('danger', 'No se puede eliminar el ticket de soporte Nº' . $pedido->getId() . ' debido a que el mismo ya se encuentra procesado.');
            }
        } catch (\Exception $e) {
            // Captura de excepciones
            $this->addFlash('danger', 'Hubo un error al intentar eliminar el pedido: ' . $e->getMessage());
        }

        return $this->redirectToRoute('ticket_index');
    }
    /**
     * Verifica si se puede eliminar un pedido basado en su historial de estados.
     *
     * @param Pedido $pedido El pedido que se desea verificar.
     * @return bool True si se puede eliminar, False si no se puede.
     */
    private function canDeletePedido(Pedido $pedido): bool
    {

        // Obtener el historial de estados del pedido
        $historialEstados = $pedido->getPedidoHistorialEstados();

        if ($historialEstados->isEmpty()) {
            return false; // No se puede eliminar si no tiene historial de estados
        }

        // Ordenar el historial de estados por fecha descendente
        $historialArray = $historialEstados->toArray();
        usort($historialArray, function ($a, $b) {
            return $b->getFecha() <=> $a->getFecha();
        });

        // Obtener el estado más reciente
        $ultimoEstado = $historialArray[0]->getPedidoEstado();

        // Verificar si el último estado es 'Recibido' o 'Desestimado'
        $nombreEstado = $ultimoEstado->getNombre();

        $estadosPermitidos = ['Recibido', 'Desestimado'];

        // Verificar si el último estado está en los estados permitidos
        if (in_array($nombreEstado, $estadosPermitidos)) {
            return true; // Se puede eliminar si el estado es 'Recibido'
        }

        return false; // No se puede eliminar si el último estado no es 'Recibido' o 'Desestimado'
    }

    /**
     * @Route("/ticket/show/{id}", name="ticket_show", requirements={"id":"\d+"})
     * @IsGranted("TICKET_VER")
     */
    public function show(Request $request, Pedido $pedido): Response
    {
        $this->assertPuedeVerTicket($pedido);

        $referrer = $request->headers->get('referer');
        $page = $request->query->get('page', 1);
        $isReferrerFromIndex = $referrer && strpos($referrer, $this->generateUrl('ticket_index')) !== false;

        $pedidoEquipos = $this->pedidoEquipoRepository->findByPedidoId($pedido->getId());

        return $this->render('pedido/show.html.twig', [
            'pedido' => $pedido,
            'usuario' => parent::getUser(),
            'pedidoEquipos' => $pedidoEquipos,
            'referrer' => $isReferrerFromIndex ? $referrer : null,
            'page' => $page,

        ]);
    }

    /**
     * @Route("/ticket/edit/estado/{id}", name="ticket_edit_estado", requirements={"id":"\d+"})
     * @IsGranted("TICKET_EDITAR")
     */
    public function editEstado(Pedido $pedido, Request $request)
    {
        $this->assertPuedeVerTicket($pedido);

        $accion = $request->get('accion');
        $em = $this->getDoctrine()->getManager();
        $historial = new PedidoHistorialEstado();
        $historial->setPedido($pedido);
        $historial->setFecha(new \Datetime('now', new \DateTimeZone('America/Argentina/La_Rioja')));

        $historial->setUsuario(parent::getUser());
        $accionPermitida = true;

        if ($accion == 'desestimar') {
            if (!$this->puedeAdministrarTickets()) {
                $accionPermitida = false;
            } else {
                $historial->setPedidoEstado($this->requirePedidoEstado('Desestimado'));
                $historial->setObservacion($request->get('inputObservacionDesestimar'));
                $this->addFlash("success", "Ticket de soporte Nº" . $pedido->getId() . " fue desestimado.");
            }
        } elseif ($accion == 'demorar') {
            if (!$this->puedeGestionarTicket($pedido)) {
                $accionPermitida = false;
            } else {
                $historial->setPedidoEstado($this->requirePedidoEstado('Demorado'));
                $historial->setObservacion($request->get('inputObservacionDemorar'));
                $this->addFlash("success", "Ticket de soporte Nº" . $pedido->getId() . " establecido a Demorado.");
            }
        } elseif ($accion == 'asignar') {
            if (!$this->puedeAdministrarTickets()) {
                $accionPermitida = false;
            } else {
                $usuario = $this->userRepository->findUserById($request->get('inputTecnico'));
                if ($usuario) {
                    if (!$pedido->getTecnicoAsignado()) {
                        $historial->setObservacion($request->get('inputObservacionAsignar'));
                    } else {
                        $historial->setObservacion(
                            "Reasignado de " . $pedido->getTecnicoAsignado()->getNombre() .
                                ' ' . $pedido->getTecnicoAsignado()->getApellido() .
                                ' a ' . $usuario->getNombre() .
                                ' ' . $usuario->getApellido()
                        );
                    }
                    $pedido->setTecnicoAsignado($usuario);
                    $historial->setPedidoEstado($this->requirePedidoEstado('Asignado'));
                    $this->addFlash("success", "Ticket de soporte Nº" . $pedido->getId() . " asignado a " . $usuario->getApellido() . " " . $usuario->getNombre() . ".");
                } else {
                    $accion = null;
                    $this->addFlash("warning", "No se asignó un técnico al ticket de soporte Nº" . $pedido->getId() . ".");
                }
            }
        } elseif ($accion == 'pendiente') {
            if (!$this->puedeGestionarTicket($pedido)) {
                $accionPermitida = false;
            } else {
                $historial->setPedidoEstado($this->requirePedidoEstado('Pendiente'));
                $historial->setObservacion($request->get('inputObservacionPendiente'));
                $this->addFlash("success", "Ticket de soporte Nº" . $pedido->getId() . " establecido a Pendiente.");
            }
        } elseif ($accion == 'procesando') {
            if (!$this->puedeGestionarTicket($pedido)) {
                $accionPermitida = false;
            } else {
                $historial->setPedidoEstado($this->requirePedidoEstado('En Proceso'));
                $historial->setObservacion($request->get('inputObservacionEnProceso'));
                $this->addFlash("success", "Ticket de soporte Nº" . $pedido->getId() . " establecido como En Proceso.");
            }
        } elseif ($accion == 'resuelto') {
            if (!$this->puedeGestionarTicket($pedido)) {
                $accionPermitida = false;
            } else {
                $historial->setPedidoEstado($this->requirePedidoEstado('Resuelto'));
                $historial->setObservacion($request->get('inputObservacionResuelto'));
                $pedido->setSolucion($request->get('inputSolucion'));
                if ($request->files->get('inputAdjuntarSolucion')) {
                    if ($request->files->get('inputAdjuntarSolucion')->getMimeType() == 'application/pdf') {
                        $pedido->setSolucionAdjunto($this->serviceUpload->subirDocumentoPDF($request->files->get('inputAdjuntarSolucion'), $this->getParameter('document_directory') . 'solucion/'));
                        $this->addFlash("success", "Ticket de soporte Nº" . $pedido->getId() . " establecido como Resuelto.");
                    } else {
                        $accion = null;
                        $this->addFlash("danger", "No fue posible adjuntar el archivo seleccionado. El tipo de archivo no es PDF.");
                    }
                } else {
                    $this->addFlash("success", "Ticket de soporte Nº" . $pedido->getId() . " establecido como Resuelto.");
                }
            }
        } elseif ($accion == 'finalizado') {
            if (!$this->puedeGestionarTicket($pedido)) {
                $accionPermitida = false;
            } else {
                $historial->setPedidoEstado($this->requirePedidoEstado('Finalizado'));
                $historial->setObservacion($request->get('inputObservacionFinalizado'));
                $this->addFlash("success", "Ticket de soporte Nº" . $pedido->getId() . " establecido como Finalizado.");
            }
        } elseif ($accion == 'recursar') {
            if (!$this->puedeGestionarTicket($pedido)) {
                $accionPermitida = false;
            } else {
                $estadoAnterior = $this->obtenerEstadoPrevioADemora($pedido);
                if (!$estadoAnterior) {
                    $accion = null;
                    $this->addFlash('danger', 'No se pudo restaurar el estado anterior del ticket Nº' . $pedido->getId() . '.');
                } else {
                    $historial->setPedidoEstado($estadoAnterior);
                    $historial->setObservacion('El ticket ya no se encuentra demorado');
                    $this->addFlash('success', 'Ticket de soporte Nº' . $pedido->getId() . ' ya no se encuentra demorado.');
                }
            }
        }

        if (!$accionPermitida) {
            $this->addFlash('danger', 'No tiene permisos para realizar esta acción sobre el ticket Nº' . $pedido->getId() . '.');
            return $this->redirectToRoute('ticket_index');
        }

        if ($accion != null) {
            $em->persist($historial);
            $em->flush();
        }
        return $this->redirectToRoute('ticket_index');
    }

    /**
     * @Route("/ticket/edit/solucionAdjunto/{id}", name="ticket_edit_solucion_adjunto", requirements={"id":"\d+"})
     * @IsGranted("TICKET_EDITAR")
     */
    public function editSolucion(Pedido $pedido, Request $request)
    {
        if ($request->files->get('inputAdjuntarSolucion')) {
            if ($request->files->get('inputAdjuntarSolucion')->getMimeType() == 'application/pdf') {
                $pedido->setSolucionAdjunto($this->serviceUpload->subirDocumentoPDF($request->files->get('inputAdjuntarSolucion'), $this->getParameter('document_directory') . 'solucion/'));
                $this->pedidoRepository->add($pedido);
                $this->addFlash("success", "Adjunto modificado correctamente.");
            } else {
                $this->addFlash("danger", "No fue posible adjuntar el archivo seleccionado. El tipo de archivo no es PDF.");
            }
        } else {
            $this->addFlash("danger", "No fue posible adjuntar el archivo seleccionado.");
        }

        return $this->redirectToRoute('ticket_show', [
            'id' => $pedido->getId()
        ]);
    }

    /**
     * @Route("/ticket/edit/equipo/{id}", name="ticket_edit_equipo", requirements={"id":"\d+"})
     * @IsGranted("TICKET_EDITAR")
     */
    public function editEquipo(Pedido $pedido, Request $request, PedidoTecnicoAsignadoRepository $pedidoTecnicoAsignadoRepository)
    {
        $this->assertPuedeVerTicket($pedido);

        if (!$this->puedeGestionarTicket($pedido)) {
            throw $this->createAccessDeniedException('No tiene permisos para gestionar el equipo de este ticket.');
        }

        $equipo = $request->request->all();
        if (
            (count($equipo) > 0 && count($pedido->getPedidoTecnicoAsignados()) == 0) ||
            (count($equipo) > 0 && $this->pedidoRepository->getExisteEquipoOperativo($pedido->getId())) ||
            (count($equipo) > 0 && !$this->pedidoRepository->getExisteEquipoOperativo($pedido->getId())) ||
            (count($equipo) == 0 && $this->pedidoRepository->getExisteEquipoOperativo($pedido->getId()))
        ) {
            $valores = implode(', ', (array_values($equipo)));
            $tecnicos = $this->userRepository->findTecnicosAsignadosPedido($valores);

            foreach ($pedido->getPedidoTecnicoAsignados() as $asignacionEquipo) {
                $asignacionEquipo->setEsOperativo(false);
                $pedidoTecnicoAsignadoRepository->add($asignacionEquipo);
            }

            if ($tecnicos) {
                foreach ($tecnicos as $tecnico) {
                    $asignacionEquipo = new PedidoTecnicoAsignado();
                    $asignacionEquipo->setPedido($pedido);
                    $asignacionEquipo->setTecnicoAsignado($tecnico);
                    $asignacionEquipo->setFechaAsignacion(new \Datetime('now', new \DateTimeZone('America/Argentina/La_Rioja')));
                    $asignacionEquipo->setUsuarioAsignacion(parent::getUser());
                    $asignacionEquipo->setEsOperativo(true);
                    $pedidoTecnicoAsignadoRepository->add($asignacionEquipo);
                }
            }

            $this->addFlash("success", "Equipo de trabajo actualizado.");
            return $this->redirectToRoute('ticket_show', [
                'id' => $pedido->getId()
            ]);
        }
        $this->addFlash("warning", "No asignó ningún técnico al equipo para el ticket de soporte Nº" . $pedido->getId() . ".");
        return $this->redirectToRoute('ticket_index');
    }

    /**
     * @Route("/pdf/generator/{idPedido}", name="pdf_generator", requirements={"idPedido":"\d+"})
     * @IsGranted("TICKET_VER")
     */
    public function crearPdf($idPedido, PedidoRepository $pedidoRepository): Response
    {
        $pedidoEquipos = $this->pedidoEquipoRepository->findByPedidoId($idPedido);
        $pedido = $pedidoRepository->find($idPedido);
        if (!$pedido) {
            throw $this->createNotFoundException('Pedido no encontrado');
        }

        $this->assertPuedeVerTicket($pedido);

        if ($pedido->getSolicitante()) {
            $nombre = $pedido->getSolicitante()->getNombre();
            $apellido = $pedido->getSolicitante()->getApellido();
        } else {
            $nombre = $pedido->getSolicitanteDisplay();
            $apellido = '';
        }

        $pedidoHistorialEstadoResuelto = $this->pedidoHistorialEstadoRepository->findFechaByPedidoAndEstado($idPedido, 'Resuelto');
        $fechaResuelto = $pedidoHistorialEstadoResuelto ? $pedidoHistorialEstadoResuelto->getFecha() : null;

        $data = [
            'imageSrc' => $this->imageToBase64($this->getParameter('kernel.project_dir') . '\public\src\img\logo_secretaria.png'),
            'pedido' => $pedido,
            'pedidoEquipos' => $pedidoEquipos,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'month' => ucfirst(strftime('%B', strtotime('today'))),
            'fechaResuelto' => $fechaResuelto,

        ];

        try {
            // Renderizar la vista Twig como HTML
            $html = $this->renderView('pedido/print_nota.html.twig', $data);

            // Crear una instancia de Dompdf
            $dompdf = new Dompdf();

            // Cargar el HTML en Dompdf
            $dompdf->loadHtml($html);

            // Renderizar el PDF
            $dompdf->render();

            // Nombre seguro del archivo PDF
            $safeFilename = 'TicketN_' . $idPedido . '_' . $nombre . $apellido . '.pdf';

            // Devolver el PDF como una respuesta HTTP
            return new Response(
                $dompdf->output(),
                Response::HTTP_OK,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $safeFilename . '"'
                ]
            );
        } catch (\Exception $e) {
            // Manejar cualquier excepción que ocurra durante la generación del PDF
            return new Response('Error al generar el PDF: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function imageToBase64($path)
    {
        // Normalizar la ruta del archivo
        $path = realpath($path);

        // Verificar si el archivo existe y se puede leer
        if (!file_exists($path) || !is_readable($path)) {
            throw new \InvalidArgumentException('El archivo no existe o no se puede leer.');
        }

        // Obtener el tipo MIME de la imagen
        $type = mime_content_type($path);

        // Verificar si el archivo es una imagen
        if (strpos($type, 'image/') !== 0) {
            throw new \InvalidArgumentException('El archivo no es una imagen válida.');
        }

        // Leer el contenido del archivo
        $data = file_get_contents($path);

        // Codificar los datos a base64
        $base64 = 'data:' . $type . ';base64,' . base64_encode($data);

        return $base64;
    }

    private function requirePedidoEstado(string $nombre): PedidoEstado
    {
        $estado = $this->pedidoEstadoRepository->findOneByNombre($nombre);

        if (!$estado) {
            throw $this->createNotFoundException(
                sprintf('El estado de ticket "%s" no está configurado. Ejecute las fixtures o cargue los estados iniciales.', $nombre)
            );
        }

        return $estado;
    }

    private function puedeAdministrarTickets(): bool
    {
        return $this->isGranted('TICKET_ASIGNAR');
    }

    private function esTecnicoAsignado(Pedido $pedido): bool
    {
        $user = $this->getUser();
        $tecnicoAsignado = $pedido->getTecnicoAsignado();

        return $user && $tecnicoAsignado && $tecnicoAsignado->getId() === $user->getId();
    }

    private function puedeGestionarTicket(Pedido $pedido): bool
    {
        return $this->puedeAdministrarTickets() || $this->esTecnicoAsignado($pedido);
    }

    private function assertPuedeVerTicket(Pedido $pedido): void
    {
        $user = $this->getUser();

        if (!$this->puedeAdministrarTickets() && $user->getIsTecnico() && !$this->esTecnicoAsignado($pedido)) {
            throw $this->createAccessDeniedException('No tiene acceso a este ticket.');
        }
    }

    private function obtenerEstadoPrevioADemora(Pedido $pedido): ?PedidoEstado
    {
        $historialOrdenado = $pedido->getPedidoHistorialEstadosOrdenados();

        for ($i = count($historialOrdenado) - 2; $i >= 0; $i--) {
            $estado = $historialOrdenado[$i]->getPedidoEstado();
            if ($estado && $estado->getNombre() !== 'Demorado') {
                return $estado;
            }
        }

        return null;
    }

    private function sincronizarUbicacionTexto(Pedido $pedido): void
    {
        if ($pedido->getUbicacionPedido() && !$pedido->getUbicacionTexto()) {
            $pedido->setUbicacionTexto($pedido->getUbicacionPedido()->getNomenclatura());
        }
    }
}
