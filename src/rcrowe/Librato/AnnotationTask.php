<?php

/**
 * Send a (deployment) annotation to Librato using Phing.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @copyright Copyright (c) 2012, Rob Crowe.
 * @license MIT
 */

namespace rcrowe\Librato;

use Task;
use BuildException;
use Project;
use Metrics\Client;

/**
 * Phing task to send an annotation to Librato.
 *
 * Built to log deployments on Librato graphs.
 */
class AnnotationTask extends Task
{
    /**
     * @var string Librato username.
     */
    protected $username;

    /**
     * @var string Librato API key.
     */
    protected $password;

    /**
     * @var string Annotation name.
     */
    protected $name;

    /**
     * @var string Annotation title, displayed on hover.
     */
    protected $title;

    /**
     * @var string Annotation description, displayed on hover.
     */
    protected $description;

    /**
     * @var bool On failure should we abort. If false Phing continues.
     */
    protected $haltOnFailure = false;

    /**
     * Set the Librato username.
     *
     * @param string $username Librato username.
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get the Librato username.
     *
     * @throws BuildException When username has not been set.
     * @return string
     */
    public function getUsername()
    {
        $username = ($this->username !== null) ? $this->username : $this->getProject()->getProperty('librato.username');

        if ($username === null) {
            throw new BuildException('Username is not set');
        }

        return $username;
    }

    /**
     * Set the Librato password.
     *
     * @param string $password Librato password.
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get the Librato password.
     *
     * @throws BuildException When password has not been set.
     * @return string
     */
    public function getPassword()
    {
        $password = ($this->password !== null) ? $this->password : $this->getProject()->getProperty('librato.password');

        if ($password === null) {
            throw new BuildException('Password is not set');
        }

        return $password;
    }

    /**
     * Set the name of this annotation.
     *
     * @param string $name Annotation name.
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the name of this annotation.
     *
     * @throws BuildException When no name has been set.
     * @return string
     */
    public function getName()
    {
        if ($this->name === null) {
            throw new BuildException('Name is not set');
        }

        return $this->name;
    }

    /**
     * Set the title of this annotation.
     *
     * @param string $title Annotation title. Shown on hover.
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get the title of this annotation.
     *
     * @throws BuildException When the title has not been set.
     * @return string
     */
    public function getTitle()
    {
        if ($this->title === null) {
            throw new BuildException('Title is not set');
        }

        return $this->title;
    }

    /**
     * Set the description for this annotation.
     *
     * @param string $description Annotation description. Shown on hover.
     * @return string
     */
    public function setDesc($description)
    {
        $this->description = $description;
    }

    /**
     * Get the description of this annotation.
     *
     * @return string
     */
    public function getDesc()
    {
        return $this->description;
    }

    /**
     * Set whether to exit on error.
     *
     * @param bool $halt Exit on error, default FALSE.
     * @return void
     */
    public function setHaltOnFailure($halt)
    {
        $this->haltOnFailure = $halt;
    }

    /**
     * Kicks everything off
     *
     * @return void
     */
    public function main()
    {
        $username    = $this->getUsername();
        $password    = $this->getPassword();
        $name        = $this->getName();
        $title       = $this->getTitle();
        $description = $this->getDesc();

        // Build up the data we are sending to Librato
        $data = array(
            'title'      => $title,
            'start_time' => time(),
        );

        if ($description !== null) {
            $data['description'] = $description;
        }

        // Send annotation to Librato
        $client = new Client($username, $password);
        $result = $client->post('/annotations/'.$name, $data);

        // Did Librato accept the annotation
        if (!isset($result->errors)) {
            // Successful
            $this->log('Successfully sent annotation to Librato', Project::MSG_INFO);
        } else {
            // Failed
            $msg = 'Failed to send annotation to Librato: '.$result->errors->request[0];

            if ($this->haltOnFailure) {
                throw new BuildException($msg);
            } else {
                $this->log($msg, Project::MSG_ERR);
            }
        }
    }
}