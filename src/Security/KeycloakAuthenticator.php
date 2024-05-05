<?php
// Définition de l'espace de noms pour le contrôleur de sécurité
namespace App\Security;

// Importation des classes nécessaires
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Authentificateur pour Keycloak
 *
 * @Test1
 */
class KeycloakAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface {
    
    private $clientRegistry;
    private $entityManager;
    private $router;

    // Constructeur avec injection des dépendances nécessaires
    public function __construct(ClientRegistry $clientRegistry, 
            EntityManagerInterface $entityManager, RouterInterface $router){
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;        
    }
    
    // Méthode d'authentification utilisant Keycloak
    public function authenticate(Request $request): Passport {
        $client = $this->clientRegistry->getClient('keycloak'); // Obtention du client Keycloak
        $accessToken = $this->fetchAccessToken($client); // Récupération du token d'accès
        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client){
                /** @var KeycloakUser $keycloakUser */
                $keycloakUser = $client->fetchUserFromToken($accessToken); // Récupération de l'utilisateur depuis Keycloak
                // Recherche de l'utilisateur dans la base de données par son identifiant Keycloak
                $existingUser = $this->entityManager
                        ->getRepository(User::class)
                        ->findOneBy(['keycloakId' => $keycloakUser->getId()]);
                if($existingUser){
                    return $existingUser; // Retour de l'utilisateur existant
                }
                // Liaison avec un utilisateur existant qui n'a pas encore de liaison Keycloak
                $email = $keycloakUser->getEmail();
                /** @var User $userInDatabase */
                $userInDatabase = $this->entityManager
                        ->getRepository(User::class)
                        ->findOneBy(['email' => $email]);
                if($userInDatabase){
                    $userInDatabase->setKeycloakId($keycloakUser->getId());
                    $this->entityManager->persist($userInDatabase);
                    $this->entityManager->flush();
                    return $userInDatabase;
                }
                // Création d'un nouvel utilisateur si non trouvé
                $user = new User();
                $user->setKeycloakId($keycloakUser->getId());
                $user->setEmail($keycloakUser->getEmail());
                $user->setPassword(""); // Aucun mot de passe car la gestion se fait via Keycloak
                $user->setRoles(['ROLE_ADMIN']);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return $user;
            })
        );
    }

    // Gestion de l'échec de l'authentification
    public function onAuthenticationFailure(Request $request, 
            AuthenticationException $exception): ?Response {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData()); // Formatage du message d'erreur
        return new Response($message, Response::HTTP_FORBIDDEN); // Réponse avec le message d'erreur
    }

    // Gestion du succès de l'authentification
    public function onAuthenticationSuccess(Request $request, 
            TokenInterface $token, string $firewallName): ?Response {
        $targetUrl = $this->router->generate('admin.formations'); // URL de redirection après authentification réussie
        return new RedirectResponse($targetUrl); // Redirection vers l'URL cible
    }

    // Méthode pour démarrer le processus d'authentification
    public function start(Request $request, AuthenticationException $authException = null): Response {
        return new RedirectResponse(
                '/oauth/login', // URL de redirection pour le login
                Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    // Vérification de la prise en charge de la requête par l'authentificateur
    public function supports(Request $request): ?bool {
        return $request->attributes->get('_route') === 'oauth_check'; // Vérifie si la route correspond à 'oauth_check'
    }

}