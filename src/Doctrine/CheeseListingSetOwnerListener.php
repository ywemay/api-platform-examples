<?php

namespace App\Doctrine;

use App\Entity\CheeseListing;
use Symfony\Component\Security\Core\Security;

/* Having a CheeseListing - no matter if it's being saved as part of an API call
 or in some other part of my system - and the owner is null, automatically
 setting the owner makes sense. Thus using Doctrine event listener.

 To make it work append the following annotation to your entity:
 @ORM\EntityListeners({"App\Doctrine\CheeseListingSetOwnerListener"})

 SEE: config/services.yml - additional configuration required:
 
    App\Doctrine\CheeseListingSetOwnerListener:
        tags: [doctrine.orm.entity_listener]
 */
class CheeseListingSetOwnerListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /*
    You can also add other methods like postPersist(), preUpdate() or preRemove()
     */

    public function prePersist(CheeseListing $cheeseListing)
    {
        // if owner is already set - do nothing, do not overwrite
        if ($cheeseListing->getOwner()) {
            return;
        }

        if ($this->security->getUser()) {
            $cheeseListing->setOwner($this->security->getUser());
        }
    }
}
