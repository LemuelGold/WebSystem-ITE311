<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Role-based Authorization Filter
 * Ensures users can only access resources they're authorized for
 */
class RoleFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of 
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Please login to access this page.');
            return redirect()->to(base_url('login'));
        }
        
        $userRole = $session->get('role');
        $uri = $request->getUri()->getPath();
        
        // Role-based access control
        if (strpos($uri, 'admin/') !== false && $userRole !== 'admin') {
            $session->setFlashdata('error', 'Access denied. Administrator privileges required.');
            return $this->redirectToRoleDashboard($userRole);
        }
        
        if (strpos($uri, 'teacher/') !== false && $userRole !== 'teacher') {
            $session->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return $this->redirectToRoleDashboard($userRole);
        }
        
        if (strpos($uri, 'student/') !== false && $userRole !== 'student') {
            $session->setFlashdata('error', 'Access denied. Student privileges required.');
            return $this->redirectToRoleDashboard($userRole);
        }
        
        return null;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No after processing needed
    }
    
    /**
     * Redirect user to their appropriate dashboard based on role
     */
    private function redirectToRoleDashboard(string $role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->to(base_url('admin/dashboard'));
            case 'teacher':
                return redirect()->to(base_url('teacher/dashboard'));
            case 'student':
                return redirect()->to(base_url('student/dashboard'));
            default:
                return redirect()->to(base_url('login'));
        }
    }
}