<?php

namespace App\Controller;

use App\Entity\UsuarioEquipo;
use App\Entity\Equipo;
use App\Entity\EquipoHistorial;

use App\Form\EquipoType;
use App\Form\Equipo\FiltroHistorialUsuarioEquipoType;
use App\Form\Equipo\FiltroHistorialIpHostType;
use App\Form\Equipo\FiltroHistorialUbicacionType;
use App\Form\Equipo\CroquisType;
use App\Form\Equipo\FiltroType;

use App\Repository\EquipoRepository;
use App\Repository\EquipoHistorialRepository;
use App\Repository\PersonalRepository;
use App\Repository\UsuarioEquipoRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use App\Services\GenerateDocument;
use App\Services\GenerateExcelDocument;

use DateTime;
use DateInterval;

/**
 * @Route("/equipo")
 */
class EquipoController extends AbstractController
{
    private $generateDocument;
    private $usuarioEquipoRepository;
    private $equipoHistorialRepository;
    private $equipoRepository;
    private $personalRepository;
    private $entityManager;
    private $paginator;
    private $encoders;
    private $normalizers;
    private $serializer;
    private $security;


    public function __construct(GenerateDocument $generateDocument, UsuarioEquipoRepository $usuarioEquipoRepository, EquipoHistorialRepository $equipoHistorialRepository, EquipoRepository $equipoRepository, PersonalRepository $personalRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginator, Security $security)
    {
        $this->generateDocument = $generateDocument;
        $this->equipoHistorialRepository = $equipoHistorialRepository;
        $this->usuarioEquipoRepository = $usuarioEquipoRepository;
        $this->equipoRepository = $equipoRepository;
        $this->personalRepository = $personalRepository;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->security = $security;
    }

    /**
     * @Route("/", name="index_equipo", methods={"GET"})
     * @IsGranted("EQUIPO_VER")
     */
    public function index(EquipoRepository $equipoRepository, Request $request): Response
    {
        $formFiltro = $this->createForm(FiltroType::class);
        if ($request->query->get($formFiltro->getName())) {
            $formFiltro->handleRequest($request);
        }
        $objOptions = $formFiltro->getData();
        $equipo = $this->equipoRepository->findForActionIndex($objOptions);
        $pagination = $this->paginator->paginate(
            $equipo,
            $request->query->get('page', 1),
            12
        );
        // Verifica si viene el id del equipo
        if ($request->query->get('id')) {
            // Muestra los equipos a vincular
            return $this->render('equipo/eleccion_equipo.html.twig', [
                "id" => $request->query->get('id'),
                "pagination" => $pagination,
                "formFiltro" => $formFiltro->createView()
            ]);
        } else {
            // muestra todos los equipos
            return $this->render('equipo/index.html.twig', [
                "pagination" => $pagination,
                "formFiltro" => $formFiltro->createView()
            ]);
        }
    }

    /**
     * @Route("/new", name="new_equipo", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_CREAR")
     */
    public function new(Request $request, EquipoRepository $equipoRepository): Response
    {
        $referrer = $request->headers->get('referer');
        $page = $request->query->get('page', 1);
        $isReferrerFromIndex = $referrer && strpos($referrer, $this->generateUrl('index_equipo')) !== false;

        $equipo = new Equipo();

        $form = $this->createForm(EquipoType::class, $equipo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userModificador = $this->security->getUser();
            $now = new Datetime();
            $equipoRepository->add($equipo);

            // Historial de Ubicación.
            $equipoHistorialUbicacion = new EquipoHistorial();
            $equipoHistorialUbicacion->setEquipo($equipo);
            $equipoHistorialUbicacion->setUbicacion($equipo->getUbicacion());
            $equipoHistorialUbicacion->setEsUbicacion(true);
            $equipoHistorialUbicacion->setModificadoPor($userModificador);
            $equipoHistorialUbicacion->setFechaInicio($now);

            $this->entityManager->persist($equipoHistorialUbicacion);

            // Historial de Host e IP.
            $equipoHistorialIdentificacion = new EquipoHistorial();
            $equipoHistorialIdentificacion->setEquipo($equipo);
            $equipoHistorialIdentificacion->setIp($equipo->getIp());
            $equipoHistorialIdentificacion->setHost($equipo->getNombre());
            $equipoHistorialIdentificacion->setEsUbicacion(false);
            $equipoHistorialIdentificacion->setModificadoPor($userModificador);
            $equipoHistorialIdentificacion->setFechaInicio($now);

            $this->entityManager->persist($equipoHistorialIdentificacion);
            $this->entityManager->flush();

            $this->addFlash("success", "Equipo '" . $equipo->getNombre() . "' creado correctamente.");
            return $this->redirectToRoute('show_equipo', [
                'id' => $equipo->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipo/new.html.twig', [
            'equipo' => $equipo,
            'form' => $form->createView(),
            'referrer' => $isReferrerFromIndex ? $referrer : null,
            'page' => $page,

        ]);
    }

    /**
     * @Route("/{id}/show", name="show_equipo", methods={"GET"})
     * @IsGranted("EQUIPO_VER")
     */
    public function show(Equipo $equipo, Request $request): Response
    {
        $referrer = $request->headers->get('referer');
        $page = $request->query->get('page', 1);
        $isReferrerFromIndex = $referrer && strpos($referrer, $this->generateUrl('index_equipo')) !== false;

        //recupera el nombre del tipo del equipo('DVR', 'Reloj', 'Camara', 'Computadora de Escritorio')
        $tipoEquipo = $equipo->getTipo()->getNombre();

        return $this->render('equipo/show.html.twig', [
            'equipo' => $equipo,
            'tipoEquipo' => $tipoEquipo,
            'referrer' => $isReferrerFromIndex ? $referrer : null,
            'page' => $page,

        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_equipo", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function edit(Request $request, Equipo $equipo, EquipoRepository $equipoRepository, EquipoHistorialRepository $equipoHistorialRepository): Response
    {
        $referrer = $request->headers->get('referer');
        $page = $request->query->get('page', 1);
        $isReferrerFromIndex = $referrer && strpos($referrer, $this->generateUrl('index_equipo')) !== false;

        // Guardar los valores originales antes de manejar el formulario
        $originalUbicacion = $equipo->getUbicacion();
        $originalIp = $equipo->getIp();
        $originalHost = $equipo->getNombre();

        // Crear y manejar el formulario
        $form = $this->createForm(EquipoType::class, $equipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userModificador = $this->security->getUser();
            $now = new DateTime('now', new \DateTimeZone('America/Argentina/La_Rioja'));

            // Si se modificó la ubicación
            if ($originalUbicacion !== $equipo->getUbicacion()) {
                $ultimoHistorialUbicacion = $equipoHistorialRepository->findUltimoHistorial($equipo, true);

                if ($ultimoHistorialUbicacion) {
                    $ultimoHistorialUbicacion->setFechaFin($now);
                    $this->entityManager->persist($ultimoHistorialUbicacion);
                }

                $equipoHistorial = new EquipoHistorial();
                $equipoHistorial->setEquipo($equipo);
                $equipoHistorial->setModificadoPor($userModificador);
                $equipoHistorial->setFechaInicio($now);
                $equipoHistorial->setUbicacion($equipo->getUbicacion());
                $equipoHistorial->setEsUbicacion(true);
                $this->entityManager->persist($equipoHistorial);
            }

            // Si se modificaron IP o Host
            if ($originalIp !== $equipo->getIp() || $originalHost !== $equipo->getNombre()) {
                $ultimoHistorialRed = $equipoHistorialRepository->findUltimoHistorial($equipo, false);

                if ($ultimoHistorialRed) {
                    $ultimoHistorialRed->setFechaFin($now);
                    $this->entityManager->persist($ultimoHistorialRed);
                }

                $equipoHistorial = new EquipoHistorial();
                $equipoHistorial->setEquipo($equipo);
                $equipoHistorial->setModificadoPor($userModificador);
                $equipoHistorial->setFechaInicio($now);
                $equipoHistorial->setIp($equipo->getIp());
                $equipoHistorial->setHost($equipo->getNombre());
                $equipoHistorial->setEsUbicacion(false);
                $this->entityManager->persist($equipoHistorial);
            }

            // Actualizar el equipo en la base de datos
            $equipoRepository->add($equipo);
            $this->entityManager->flush();

            $this->addFlash("success", "Equipo editado correctamente.");
            return $this->redirectToRoute('show_equipo', ['id' => $equipo->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipo/new.html.twig', [
            'page' => $page,
            'equipo' => $equipo,
            'form' => $form->createView(),
            'referrer' => $isReferrerFromIndex ? $referrer : null,
        ]);
    }

    /**
     * @Route("/{id}", name="delete_equipo", methods={"DELETE"})
     * @IsGranted("EQUIPO_ELIMINAR")
     */
    public function delete(Request $request, Equipo $equipo, EquipoRepository $equipoRepository): Response
    {
        // Solo bloquea si hay usuarios actuales o planilla
        $usuariosActuales = 0;
        foreach ($equipo->getUsuarios() as $usuarioEquipo) {
            if ($usuarioEquipo->getIsActual()) {
                $usuariosActuales++;
            }
        }
        $tienePlanilla = $equipo->getPlanillaEquipo() !== null;
        $equipoNombre = $equipo->getNombre();
        // Si el equipo tiene elementos asociados no se puede eliminar
        if ($usuariosActuales > 0 || $tienePlanilla) {
            $this->addFlash("danger", "El equipo " . $equipo->getNombre() . " no puede ser eliminado porque tiene usuarios actuales o planilla asociada. Dé de baja los usuarios o elimine la planilla antes.");
        } else {
            if ($this->isCsrfTokenValid('delete' . $equipo->getId(), $request->request->get('_token'))) {
                // Limpia historial de usuarios (bajas) para no romper FK
                foreach ($equipo->getUsuarios()->toArray() as $usuarioEquipo) {
                    $this->entityManager->remove($usuarioEquipo);
                }
                // Si el equipo tiene croquis, elimina el archivo tambien
                if ($equipo->getCroquis()) {
                    $fileName = $this->getParameter('document_directory') . $equipo->getCroquis();
                    if (file_exists($fileName)) {
                        unlink($fileName);
                    }
                }
                $equipoRepository->remove($equipo);
                $this->addFlash("success", "El equipo '$equipoNombre' fue eliminado correctamente.");
            }
        }

        return $this->redirectToRoute('index_equipo', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/subir_croquis", name="subir_croquis_equipo", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function subirCroquis(Request $request, Equipo $equipo, EquipoRepository $equipoRepository): Response
    {
        $form = $this->createForm(CroquisType::class, $equipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $croquisFile = $form['croquis']->getData();
            $newFilename = $this->subirArchivo($croquisFile, $equipo->getId());
            $equipo->setCroquis($newFilename);
            $equipoRepository->add($equipo);
            $this->addFlash("success", "Croquis subido correctamente.");
            return $this->redirectToRoute('show_equipo', [
                'id' => $equipo->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipo/subir_croquis.html.twig', [
            'equipo' => $equipo,
            'form' => $form->createView(),
        ]);
    }

    // Subir archivo al directorio correspondiente a la repartición
    public function subirArchivo($brochureFile, $id)
    {
        // Armamos el nombre del archivo
        $newFilename = "croquis_" . $id . "." . $brochureFile->guessExtension();

        //comprobamos de exite el directorio antes de guardar el archivo
        $dir = $this->getParameter('document_directory') . '/croquis/equipo/';

        if (!file_exists($dir)) {
            //si no existe lo creamos
            mkdir($dir);
        }
        // Varifica si existe un arvhivo, si existe lo elimina
        $fileName = $dir . '/' . $newFilename;

        if (file_exists($fileName)) {
            unlink($fileName);
        }
        // Mueve el archivo al directorio del equipo
        $brochureFile->move($dir, $newFilename);

        return $newFilename;
    }

    /**
     * @Route("/{id}/agregar_personal", name="agregar_personal_equipo", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function agregarPersonal(Request $request, Equipo $equipo, EquipoRepository $equipoRepository, PersonalRepository $personalRepository, UsuarioEquipoRepository $userEquipoRepository): Response
    {

        // Consulta si viene el id del personal  
        if ($request->query->get('idP')) {
            // Recupera los datos del personal
            $personal = $personalRepository->find($request->get('idP'));
            if (!$personal) {
                $this->addFlash("danger", "El personal seleccionado no existe.");
                return $this->redirectToRoute('show_equipo', ['id' => $equipo->getId()], Response::HTTP_SEE_OTHER);
            }

            $personalNombre = $personal->getNombre() . ', ' . $personal->getApellido();

            //Consultar si usuario ya tiene a cargo el equipo
            if ($userEquipoRepository->usuarioActual($request->get('idP'), $equipo->getId())) {
                $this->addFlash("danger", "El usuario ya tiene a cargo el equipo.");
                return $this->redirectToRoute('show_equipo', ['id' => $equipo->getId()], Response::HTTP_SEE_OTHER);
            } else {
                // Guarda el registro de usuario de equipo
                $usuario = new UsuarioEquipo();
                $usuario->setUsuario($personal);
                $userModificador = $this->security->getUser();
                $usuario->setModificadoPor($userModificador);
                $usuario->setEquipo($equipo);
                $usuario->setFechaInicio(new Datetime('now', new \DateTimeZone('America/Argentina/La_Rioja')));

                $this->entityManager->persist($usuario);
                $this->entityManager->flush();
                $this->addFlash("success", "El usuario '$personalNombre' se agregó correctamente.");
            }
        }

        return $this->redirectToRoute('show_equipo', ['id' => $equipo->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/traspaso_personal", name="traspaso_personal_equipo", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function traspasoEquipo(Request $request, Equipo $equipo, PersonalRepository $personalRepository, UsuarioEquipoRepository $userEquipoRepository): Response
    {
        // Consulta si viene el id del personal nuevo
        $idPersonalNuevo = $request->query->get('idP');

        $equipoNombre = $equipo->getNombre();

        if ($idPersonalNuevo) {
            // Recupera el personal nuevo
            $personalNuevo = $personalRepository->find($idPersonalNuevo);

            if (!$personalNuevo) {
                $this->addFlash("danger", "El personal seleccionado no existe.");
                return $this->redirectToRoute('show_equipo', ['id' => $equipo->getId()], Response::HTTP_SEE_OTHER);
            }

            $personalNombre = $personalNuevo->getNombre() . ', ' . $personalNuevo->getApellido();

            // Actualizar los usuarios anteriores como no actuales
            $usuariosAnteriores = $userEquipoRepository->findBy(['equipo' => $equipo, 'isActual' => true]);
            foreach ($usuariosAnteriores as $usuarioAnterior) {
                $usuarioAnterior->setIsActual(false);
                $usuarioAnterior->setFechaFin(new DateTime('now', new \DateTimeZone('America/Argentina/La_Rioja')));
                $this->entityManager->persist($usuarioAnterior);
            }
            if ($userEquipoRepository->usuarioActual((int) $idPersonalNuevo, $equipo->getId())) {
                $this->addFlash("danger", "El usuario ya tiene a cargo el equipo.");
                return $this->redirectToRoute('show_equipo', ['id' => $equipo->getId()], Response::HTTP_SEE_OTHER);
            } else {
                // Agregar el nuevo usuario al equipo
                $nuevoUsuario = new UsuarioEquipo();
                $nuevoUsuario->setUsuario($personalNuevo);

                $nuevoUsuario->setEquipo($equipo);
                $nuevoUsuario->setFechaInicio(new DateTime('now', new \DateTimeZone('America/Argentina/La_Rioja')));

                $userModificador = $this->security->getUser();
                $nuevoUsuario->setModificadoPor($userModificador);

                $this->entityManager->persist($nuevoUsuario);
                $this->entityManager->flush();
            }
            $this->addFlash("success", "El equipo se traspasó correctamente al personal '$personalNombre'.");
        } else {
            $this->addFlash("danger", "No se ha seleccionado ningún personal para el traspaso.");
        }

        return $this->redirectToRoute('show_equipo', ['id' => $equipo->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/eliminar_personal", name="eliminar_personal_equipo", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function eliminarPersonal(Request $request, UsuarioEquipo $user, EquipoRepository $equipoRepository, PersonalRepository $personalRepository): Response
    {
        // Recupera el id del equipo
        $id = $user->getEquipo()->getId();
        $personalNombre = $user->getUsuario()->getNombre() . ', ' . $user->getUsuario()->getApellido();
        // eliminar el usuario del equipo
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->addFlash("success", "Se eliminó el usuario '$personalNombre' correctamente.");

        return $this->redirectToRoute('show_equipo', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/bajar_usuario", name="bajar_personal_equipo", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function bajarUsuario(Request $request, UsuarioEquipo $user): Response
    {
        // Actualiza el estado del registro.
        $user->setFechaFin(new DateTime());
        $user->setModificadoPor(parent::getUser());
        $user->setIsActual(false);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $this->addFlash("success", "Historial de usuarios actualizado.");

        return $this->redirectToRoute('show_equipo', [
            'id' => $user->getEquipo()->getId()
        ], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/vincular_equipo", name="vincular_equipo", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function vincularEquipo(Request $request, Equipo $equipo, EquipoRepository $equipoRepository): Response
    {
        // Verifica si viene el id del Equipo
        if ($request->query->get('idE')) {
            // Recupera los datos del equipo
            $eq = $equipoRepository->find($request->get('idE'));

            if (!$eq) {
                $this->addFlash("danger", "El equipo a vincular no existe.");
            } elseif ($equipo->getId() == $eq->getId()) {
                $this->addFlash("danger", "El equipo no se puede vincular a sí mismo.");
            } else {
                // Guarda el equipo vinculado
                $equipo->addChild($eq);
                $this->entityManager->persist($equipo);
                $this->entityManager->flush();
                $this->addFlash("success", "Equipo vinculado correctamente.");
            }
        }

        return $this->redirectToRoute('show_equipo', ['id' => $equipo->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/desvincular_equipo", name="desvincular_equipo", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function desvincularEquipo(Request $request, Equipo $equipo, EquipoRepository $equipoRepository, PersonalRepository $personalRepository): Response
    {
        // Recupera el id del equipo padre
        $id = $equipo->getParent()->getId();
        // Recupera los datos del equipo padre
        $eq = $equipoRepository->find($id);
        // Elimina el equipo vinculado
        $eq->removeChild($equipo);
        $this->entityManager->persist($eq);
        $this->entityManager->flush();
        $this->addFlash("success", "Equipo desvinculado correctamente.");

        return $this->redirectToRoute('show_equipo', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/equipo_historial", name="equipo_historial_index", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_VER")
     */
    public function indexHistorial(Request $request, Equipo $equipo): Response
    {
        // Filtro para historial de usuarios
        $formFiltroUsuario = $this->createForm(FiltroHistorialUsuarioEquipoType::class);
        if ($request->query->get($formFiltroUsuario->getName())) {
            $formFiltroUsuario->handleRequest($request);
        }
        $filtroUsuario = $formFiltroUsuario->getData();
        $usuarios = $this->usuarioEquipoRepository->findForActionIndex($equipo, $filtroUsuario);

        $usuariosPagination = $this->paginator->paginate(
            $usuarios,
            $request->query->get('usuarios_page', 1)
        );

        // Filtro para historial de ubicaciones
        $formFiltroUbicacion = $this->createForm(FiltroHistorialUbicacionType::class);
        if ($request->query->get($formFiltroUbicacion->getName())) {
            $formFiltroUbicacion->handleRequest($request);
        }
        $filtroUbicacion = $formFiltroUbicacion->getData();
        $ubicaciones = $this->equipoHistorialRepository->findForUbicaciones($filtroUbicacion, $equipo);

        $ubicacionPagination = $this->paginator->paginate(
            $ubicaciones,
            $request->query->get('ubicacion_page', 1)
        );

        // Filtro para historial de IP y Host
        $formFiltroIpHost = $this->createForm(FiltroHistorialIpHostType::class);
        if ($request->query->get($formFiltroIpHost->getName())) {
            $formFiltroIpHost->handleRequest($request);
        }
        $filtroIpHost = $formFiltroIpHost->getData();
        $ipHost = $this->equipoHistorialRepository->findForIpYHost($filtroIpHost, $equipo);

        $ipHostPagination = $this->paginator->paginate(
            $ipHost,
            $request->query->get('ip_host_page', 1)
        );

        // Renderizado de la plantilla
        return $this->render('equipo/_historial.html.twig', [
            'equipo' => $equipo,
            'usuariosPagination' => $usuariosPagination,
            'ubicacionPagination' => $ubicacionPagination,
            'ipHostPagination' => $ipHostPagination,
            'formFiltroUsuario' => $formFiltroUsuario->createView(),
            'formFiltroUbicacion' => $formFiltroUbicacion->createView(),
            'formFiltroIpHost' => $formFiltroIpHost->createView(),
        ]);
    }

    /**
     * @Route("/equipo/cambiar_condicion/{id}", name="equipo_cambiar_condicion", methods={"POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function cambiarCondicion(Request $request, Equipo $equipo): Response
    {
        $nuevoEstado = $request->request->get('condicion');
        if (!in_array($nuevoEstado, [1, 2, 3])) {
            $this->addFlash('error', 'Estado inválido.');
            return $this->redirectToRoute('index_equipo');
        }

        $equipo->setCondicion($nuevoEstado);
        $equipoNombre = $equipo->getNombre();

        // Guardar los cambios
        $this->getDoctrine()->getManager()->flush();
        $estados = [
            1 => 'Activo',
            2 => 'Prestado',
            3 => 'Fuera de Servicio',
        ];

        if (isset($estados[$nuevoEstado])) {
            $this->addFlash('success', "Equipo '$equipoNombre' designado como '{$estados[$nuevoEstado]}'.");
        }

        return $this->redirectToRoute('index_equipo');
    }

    /**
     * @Route("/print_equipo/{id}", name="equipos_info_print")
     * @IsGranted("EQUIPO_VER")
     */
    public function printEquipo(Request $request, int $id, EquipoRepository $equipoRepository, GenerateDocument $generateDocument): Response
    {
        $equipo = $equipoRepository->find($id);

        if (!$equipo) {
            throw $this->createNotFoundException('El equipo no existe');
        }

        $requestedBlocks = [
            'show_especificaciones' => $request->query->get('show_especificaciones', false),
            'show_almacenamiento' => $request->query->get('show_almacenamiento', false),
            'show_sistemas_operativos' => $request->query->get('show_sistemas_operativos', false),
            'show_accesos' => $request->query->get('show_accesos', false),
            'show_historial' => $request->query->get('show_historial', false),
        ];

        $equipoNombre = $equipo->getNombre();
        $fechaActual = new DateTime('now', new \DateTimeZone('America/Argentina/La_Rioja'));
        $filename = $equipoNombre . '-' . $fechaActual->format('Y-m-d_H-i-s') . '.pdf';

        try {
            $pdfContent = $generateDocument->generateEquipoInfo($equipo, null, $requestedBlocks);
        } catch (\Throwable $e) {
            return new Response('Error al generar el PDF: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response(
            $pdfContent,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]
        );
    }

    /**
     * @Route("/print", name="equipos_print")
     * @IsGranted("EQUIPO_VER")
     */
    public function printEquipos(Request $request, EquipoRepository $equipoRepository, GenerateDocument $generateDocument): Response
    {
        // Obtiene los equipos dependiendo de los filtros aplicados
        $filterForm = $this->createForm(FiltroType::class);
        $filterForm->handleRequest($request);

        // Verifica si el formulario fue enviado y es válido
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            // Obtiene los filtros aplicados
            $filters = $filterForm->getData();
        } else {
            // Filtros por defecto
            $filters = [];
        }

        $qb = $this->equipoRepository->findForActionIndex($filters);

        // Ejecuta la consulta para obtener los resultados
        $equipos = $qb->getQuery()->getResult();

        $fechaActual = new DateTime('now', new \DateTimeZone('America/Argentina/La_Rioja'));

        // Formatea la fecha como una cadena
        $formattedDate = $fechaActual->format('Y-m-d H:i:s');

        // Genera el nombre del archivo con la fecha formateada
        $filename = "equipos-$formattedDate.pdf";

        // Generar el PDF
        try {
            $pdfContent = $generateDocument->generateEquipos($equipos, null, $filters);
        } catch (\Throwable $e) {
            return new Response('Error al generar el PDF: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Devuelve el PDF como respuesta
        return new Response(
            $pdfContent,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "inline; filename=$filename",
            ]
        );
    }

    /**
     * @Route("/print/excel", name="equipos_print_excel")
     * @IsGranted("EQUIPO_VER")
     */
    public function excelEquipos(Request $request, EquipoRepository $equipoRepository, GenerateExcelDocument $generateExcel): Response
    {
        $filterForm = $this->createForm(FiltroType::class);
        $filterForm->handleRequest($request);

        $filters = $filterForm->isSubmitted() && $filterForm->isValid()
            ? $filterForm->getData()
            : [];

        $qb = $equipoRepository->findForActionIndex($filters);
        $equipos = $qb->getQuery()->getResult();

        $fechaActual = new \DateTime('now', new \DateTimeZone('America/Argentina/La_Rioja'));
        $filename = 'Listado de Equipos - ' . $fechaActual->format('Y-m-d H-i-s') . '.xlsx';

        $excelContent = $generateExcel->generateEquiposExcel($equipos, $filters);

        return new Response(
            $excelContent,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]
        );
    }

    /**
     * @Route("/ultimo-equipo/{nomenclatura}", name="ultimo_equipo", methods={"GET"})
     */
    public function obtenerUltimoEquipo($nomenclatura, EquipoRepository $equipoRepository): Response
    {
        // Consulta el último equipo con el nombre que comienza con la nomenclatura
        $ultimoEquipo = $equipoRepository->createQueryBuilder('e')
            ->where('e.nombre LIKE :nomenclatura')
            ->setParameter('nomenclatura', $nomenclatura . '%')
            ->orderBy('e.nombre', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // Inicializa variable
        $ultimoNumero = 0;

        if ($ultimoEquipo) {
            // Extrae los últimos 2 dígitos
            $ultimoNumero = (int)substr($ultimoEquipo->getNombre(), -2);
        }

        return $this->json([
            'ultimoEquipo' => $ultimoEquipo ? $ultimoEquipo->getNombre() : '-',
            'ultimoNumero' => $ultimoNumero
        ]);
    }
}
