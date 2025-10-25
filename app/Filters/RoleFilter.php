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
        
        // Get the current URL path without the base URL for better parsing
        $currentUrl = current_url();
        $baseUrl = base_url();
        $relativePath = str_replace($baseUrl, '', $currentUrl);
        
        // Extract the first path segment (role area)
        $pathParts = explode('/', trim($relativePath, '/'));
        $segment1 = $pathParts[0] ?? '';
        
        // DEBUG: Log what we're detecting
        log_message('debug', 'RoleFilter - URL: ' . $currentUrl . ' | Segment1: ' . $segment1 . ' | User Role: ' . $userRole);
        
        // If empty segment or not a role-specific route, allow access
        if (empty($segment1) || !in_array($segment1, ['admin', 'teacher', 'student'])) {
            log_message('debug', 'RoleFilter - Allowing non-role route: ' . $segment1);
            return null;
        }
        
        // Allow users to access their own role area
        if ($segment1 === $userRole) {
            log_message('debug', 'RoleFilter - Allowing ' . $userRole . ' to access their own area');
            return null;
        }
        
        // Block access to other role areas
        if ($segment1 === 'admin' && $userRole !== 'admin') {
            log_message('warning', 'Access denied: User "' . $session->get('name') . '" (role: ' . $userRole . ') tried to access admin area: ' . $currentUrl);
            $session->setFlashdata('error', 'Access denied. Administrator privileges required.');
            return $this->redirectToRoleDashboard($userRole);
        }
        
        if ($segment1 === 'teacher' && $userRole !== 'teacher') {
            log_message('warning', 'Access denied: User "' . $session->get('name') . '" (role: ' . $userRole . ') tried to access teacher area: ' . $currentUrl);
            $session->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return $this->redirectToRoleDashboard($userRole);
        }
        
        if ($segment1 === 'student' && $userRole !== 'student') {
            log_message('warning', 'Access denied: User "' . $session->get('name') . '" (role: ' . $userRole . ') tried to access student area: ' . $currentUrl);
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