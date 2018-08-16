<?php
namespace Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException;

use Exception;
use stdclass;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;

use Sfynx\AuthBundle\Infrastructure\Exception\InvalidArgumentException;

/**
 * Class HandlerExceptionFactory.
 *
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerException
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       https://github.com/pigroupe/cmf-sfynx/blob/master/web/COPYING.txt
 * @since      2014-07-18
 */
class HandlerExceptionFactory
{
    /** @var EngineInterface $templating */
    public $templating;
    /** @var RouteTranslatorFactoryInterface  */
    public $router;
    /** @var Logger  */
    protected $logger;
    /** @var string $locale The locale value */
    public $locale;
    /** @var KernelInterface $kernel */
    public $kernel;
    /** @var RequestInterface */
    public $request;
    /** @var Exception */
    public $exception;
    /** @var stdclass */
    public $param;

    /**
     * Constructor.
     *
     * @param EngineInterface    $templating
     * @param RouteTranslatorFactoryInterface $router
     * @param Logger $logger
     */
    public function __construct(
        EngineInterface $templating,
        RouteTranslatorFactoryInterface $router,
        Logger $logger
    ) {
        $this->templating = $templating;
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * Event handler that renders not found page
     * in case of a NotFoundHttpException
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @access public
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        try {
            $this->request = $event->getRequest($event);
            $this->locale  = $this->request->getLocale();
            $this->exception = $event->getException();

            $this->setError();

            $responseException = HandlerExceptionBuild::build(
                $event->getRequest()->getRequestFormat(),
                $this
            );

            //sf 3.4 : $event->allowCustomResponseCode();
            $event->setResponse($responseException->getResponse());
        } catch (InvalidArgumentException $e) {
            //In this case, we want to ignore if the the factory cannot build the exception response.
        }
    }

    /**
     * Return the HTTP code we want based on the type and the code of the exception.
     *
     * @param \Exception $exception
     * @return int
     */
    public static function getHttpCode(Exception $exception)
    {
        // For Symfony specific exceptions, overload the HTTP error code.
        if ($exception instanceof UndefinedOptionsException
            || $exception instanceof MissingOptionsException
        ) {
            // For these exception types, error code is 400 : BAD_REQUEST.
            return Response::HTTP_BAD_REQUEST;
        } elseif (0 != $exception->getCode()) {
            // HTTP status code is the exception code if defined.
            return $exception->getCode();
        }
        // Otherwise, the 500 : HTTP_INTERNAL_SERVER_ERROR is used.
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Sets parameter template values.
     *
     * @access protected
     * @return void
     */
    public function setParams(array $option)
    {
        $this->param = (object) $option;
    }

    /**
     * Return property value even if the default value given in argument
     * @param $property
     * @param $defaultValue
     * @return bool
     */
    public function getParam($property, $defaultValue)
    {
        if (\property_exists($this->param, $property)) {
            return $this->param->$property;
        }
        return $defaultValue;
    }

    /**
     * @return void
     */
    protected function setError()
    {
        // Starts logging exception messages
        $this->logger->addError($this->exception->getMessage());
        $this->logger->addError($this->exception->getTraceAsString());

        // Custom messages logged out with first level of callstack informations
        if (!\is_null($this->exception->getTrace())) {
            $exceptionStackTrace = $this->exception->getTrace();
            foreach($exceptionStackTrace[0] as $key => $exceptionStackTraceFirstLevelInfo){
                if ('args' == $key) {
                    if (isset($exceptionStackTraceFirstLevelInfo[0])) {

                        if (\method_exists($exceptionStackTraceFirstLevelInfo[0],'getRequest')) {
                            $requestParameters = $exceptionStackTraceFirstLevelInfo[0]->getRequest()->request;
                            $requestParametersString = \print_r($requestParameters,true);
                            $this->logger->addError($requestParametersString);
                        } elseif ($exceptionStackTraceFirstLevelInfo[0] instanceof \Symfony\Component\HttpFoundation\Request) {
                            $requestParameters = $exceptionStackTraceFirstLevelInfo[0]->request;
                            $requestParametersString = \print_r($requestParameters,true);
                            $this->logger->addError($requestParametersString);
                        }
                    }
                } else {
                    $this->logger->addError($key . ' : ' . $exceptionStackTraceFirstLevelInfo);
                }
            }
        }
    }
}
